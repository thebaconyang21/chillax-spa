<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $search = is_string($search) ? trim($search) : '';
        $paidTransactions = static fn (Builder $query): Builder => $query->where('payment_status', 'paid');

        $customers = Customer::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('contact_number', 'like', '%'.$search.'%');
                });
            })
            ->withCount(['transactions as total_visits' => $paidTransactions])
            ->withMax(['transactions as last_visit' => $paidTransactions], 'transaction_date')
            ->orderBy('customer_name')
            ->paginate(10)
            ->withQueryString();

        return view('customers', compact('customers', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'contact_number' => [
                'nullable',
                'string',
                Rule::regex('/^(09|\+639)\d{9}$/'),
                Rule::unique('customers', 'contact_number'),
            ],
        ], [
            'contact_number.regex' => 'Enter a valid PH mobile number, e.g. 09171234567.',
        ]);

        Customer::create($validated);

        return redirect()->route('customers')->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'contact_number' => [
                'nullable',
                'string',
                Rule::regex('/^(09|\+639)\d{9}$/'),
                Rule::unique('customers', 'contact_number')->ignore($customer),
            ],
        ], [
            'contact_number.regex' => 'Enter a valid PH mobile number, e.g. 09171234567.',
        ]);

        $customer->update($validated);

        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer): JsonResponse
    {
        $paidTransactions = static fn (Builder $query): Builder => $query->where('payment_status', 'paid');
        $customer = Customer::query()
            ->whereKey($customer->getKey())
            ->withCount(['transactions as total_visits' => $paidTransactions])
            ->withMax(['transactions as last_visit' => $paidTransactions], 'transaction_date')
            ->with([
                'appointments' => static function (HasMany $query): void {
                    $query->with(['service:id,service_name', 'therapist:id,therapist_name'])
                        ->orderByDesc('appointment_date')
                        ->orderByDesc('appointment_time')
                        ->limit(10);
                },
                'transactions' => static function (HasMany $query): void {
                    $query->where('payment_status', 'paid')
                        ->latest('transaction_date')
                        ->limit(10);
                },
            ])
            ->firstOrFail();

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'contact_number' => $customer->contact_number,
            ],
            'total_visits' => $customer->total_visits,
            'last_visit' => $customer->last_visit === null
                ? null
                : Carbon::parse($customer->last_visit)->format('M j, Y'),
            'appointments' => $customer->appointments->map(
                fn (Appointment $appointment): array => [
                    'date' => Carbon::parse($appointment->appointment_date)->format('M j, Y'),
                    'time' => Carbon::parse($appointment->appointment_time)->format('g:i A'),
                    'service_name' => $appointment->service->service_name,
                    'therapist_name' => $appointment->therapist->therapist_name,
                    'status' => $appointment->appointment_status,
                ]
            )->values(),
            'transactions' => $customer->transactions->map(
                fn (Transaction $transaction): array => [
                    'date' => Carbon::parse($transaction->transaction_date)->format('M j, Y'),
                    'total_amount' => $transaction->total_amount,
                    'payment_method' => $transaction->payment_method,
                ]
            )->values(),
        ]);
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->appointments()->exists() || $customer->transactions()->exists()) {
            return redirect()->route('customers')
                ->with('error', 'This customer has existing records and cannot be deleted.');
        }

        $customer->delete();

        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }
}
