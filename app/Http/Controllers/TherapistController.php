<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TherapistController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'week_start' => ['sometimes', 'required', 'date_format:Y-m-d'],
        ]);
        $search = $request->query('search');
        $search = is_string($search) ? trim($search) : '';
        $weekStart = isset($validated['week_start'])
            ? Carbon::createFromFormat('Y-m-d', $validated['week_start'])->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        $transactionDateRange = [$weekStart->copy()->startOfDay(), $weekEnd];
        $commissionDateRange = [$weekStart->toDateString(), $weekEnd->toDateString()];

        $completedServiceConstraint = static function (Builder $query) use ($transactionDateRange): void {
            $query->whereHas('transaction', static function (Builder $transactionQuery) use ($transactionDateRange): void {
                $transactionQuery->where('payment_status', 'paid')
                    ->whereBetween('transaction_date', $transactionDateRange);
            });
        };
        $commissionConstraint = static fn (Builder $query): Builder => $query->whereBetween('commission_date', $commissionDateRange);

        $therapists = Therapist::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('therapist_name', 'like', '%'.$search.'%');
            })
            ->withCount(['transactionDetails as completed_services' => $completedServiceConstraint])
            ->withSum(['transactionDetails as total_sales' => $completedServiceConstraint], 'subtotal')
            ->withSum(['commissions as commission' => $commissionConstraint], 'commission_amount')
            ->orderBy('therapist_name')
            ->get();

        $totalTherapists = Therapist::query()->count();
        $availableTherapists = Therapist::query()->where('availability_status', 'available')->count();
        $totalCommission = Commission::query()
            ->whereBetween('commission_date', $commissionDateRange)
            ->sum('commission_amount');

        return view('therapists', compact(
            'therapists',
            'search',
            'weekStart',
            'weekEnd',
            'totalTherapists',
            'availableTherapists',
            'totalCommission',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->therapistRules(), $this->therapistMessages());

        Therapist::create($validated);

        return redirect()->route('therapists')->with('success', 'Therapist added successfully.');
    }

    public function update(Request $request, Therapist $therapist): RedirectResponse
    {
        $validated = $request->validate($this->therapistRules(), $this->therapistMessages());

        $therapist->update($validated);

        return redirect()->route('therapists')->with('success', 'Therapist updated successfully.');
    }

    public function destroy(Therapist $therapist): RedirectResponse
    {
        if ($therapist->appointments()->exists()
            || $therapist->transactionDetails()->exists()
            || $therapist->commissions()->exists()) {
            return redirect()->route('therapists')
                ->with('error', 'This therapist has existing records. Set them to Off Duty instead.');
        }

        $therapist->getConnection()->transaction(function () use ($therapist): void {
            $therapist->schedules()->delete();
            $therapist->delete();
        });

        return redirect()->route('therapists')->with('success', 'Therapist deleted successfully.');
    }

    public function show(Therapist $therapist): JsonResponse
    {
        $therapist->load([
            'schedules' => static function (HasMany $query): void {
                $query->whereDate('schedule_date', '>=', now()->toDateString())
                    ->orderBy('schedule_date')
                    ->orderBy('start_time');
            },
            'transactionDetails' => static function (HasMany $query): void {
                $query->whereHas('transaction', static function (Builder $transactionQuery): void {
                    $transactionQuery->where('payment_status', 'paid');
                });
                $query->with([
                    'transaction:id,customer_id,transaction_date',
                    'transaction.customer:id,customer_name',
                    'service:id,service_name',
                    'commission:id,transaction_detail_id,commission_amount',
                ]);
                $query->orderByDesc(
                    Transaction::query()
                        ->select('transaction_date')
                        ->whereColumn('transactions.id', 'transaction_details.transaction_id')
                );
                $query->limit(10);
            },
        ]);

        return response()->json([
            'therapist' => [
                'id' => $therapist->id,
                'therapist_name' => $therapist->therapist_name,
                'contact_number' => $therapist->contact_number,
                'availability_status' => $therapist->availability_status,
            ],
            'schedules' => $therapist->schedules->map(
                fn (TherapistSchedule $schedule): array => [
                    'id' => $schedule->id,
                    'schedule_date' => $schedule->schedule_date,
                    'date' => Carbon::parse($schedule->schedule_date)->format('M j, Y'),
                    'start_time' => $schedule->start_time,
                    'start_label' => Carbon::parse($schedule->start_time)->format('g:i A'),
                    'end_time' => $schedule->end_time,
                    'end_label' => Carbon::parse($schedule->end_time)->format('g:i A'),
                ]
            )->values(),
            'completed_services' => $therapist->transactionDetails->map(
                fn (TransactionDetail $detail): array => [
                    'date' => Carbon::parse($detail->transaction->transaction_date)->format('M j, Y'),
                    'service_name' => $detail->service->service_name,
                    'customer_name' => $detail->transaction->customer->customer_name,
                    'subtotal' => $detail->subtotal,
                    'commission' => $detail->commission?->commission_amount,
                ]
            )->values(),
        ]);
    }

    /**
     * @return array<string, array<int, string|object>>
     */
    private function therapistRules(): array
    {
        return [
            'therapist_name' => ['required', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', Rule::regex('/^(09|\+639)\d{9}$/')],
            'availability_status' => ['required', Rule::in(['available', 'in_service', 'off_duty'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function therapistMessages(): array
    {
        return [
            'contact_number.regex' => 'Enter a valid PH mobile number, e.g. 09171234567.',
        ];
    }
}
