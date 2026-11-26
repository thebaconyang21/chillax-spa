<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Services\AppointmentScheduler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentScheduler $scheduler) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'status' => ['sometimes', 'nullable', Rule::in(['all', 'upcoming', 'arrived', 'in_service', 'completed'])],
            'search' => ['sometimes', 'nullable', 'string'],
        ]);
        $date = $validated['date'] ?? today()->toDateString();
        $status = ($validated['status'] ?? '') ?: 'all';
        $search = trim($validated['search'] ?? '');

        $appointments = Appointment::query()
            ->where('appointment_date', $date)
            ->when($status === 'upcoming', function (Builder $query): void {
                $query->whereIn('appointment_status', ['pending', 'confirmed']);
            })
            ->when(in_array($status, ['arrived', 'in_service', 'completed'], true), function (Builder $query) use ($status): void {
                $query->where('appointment_status', $status);
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->whereHas('customer', function (Builder $customerQuery) use ($search): void {
                    $customerQuery->where('customer_name', 'like', '%'.$search.'%');
                });
            })
            ->with(['customer', 'service', 'therapist'])
            ->orderBy('appointment_time')
            ->orderBy('id')
            ->get();

        $upcomingReminder = Appointment::query()
            ->where('appointment_date', today()->toDateString())
            ->whereIn('appointment_status', ['pending', 'confirmed'])
            ->where('appointment_time', '>', now()->format('H:i:s'))
            ->with(['customer', 'service', 'therapist'])
            ->orderBy('appointment_time')
            ->first();
        $minutesUntilReminder = null;

        if ($upcomingReminder !== null) {
            $reminderTime = Carbon::parse(today()->toDateString().' '.$upcomingReminder->appointment_time);
            $minutesUntilReminder = max(0, (int) ceil(($reminderTime->timestamp - now()->timestamp) / 60));
        }

        $customers = Customer::query()->orderBy('customer_name')->get(['id', 'customer_name', 'contact_number']);
        $services = Service::query()->orderBy('service_name')->get();
        $therapists = Therapist::query()->orderBy('therapist_name')->get();

        return view('appointments', compact(
            'appointments',
            'customers',
            'services',
            'therapists',
            'date',
            'status',
            'search',
            'upcomingReminder',
            'minutesUntilReminder',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->appointmentRules(), $this->appointmentMessages());
        $service = Service::query()->findOrFail($validated['service_id']);
        $therapist = Therapist::query()->findOrFail($validated['therapist_id']);

        return DB::transaction(function () use ($validated, $service, $therapist): RedirectResponse {
            $lockedTherapist = Therapist::query()
                ->whereKey($therapist->id)
                ->lockForUpdate()
                ->firstOrFail();
            $customer = $this->customerFromValidatedInput($validated);
            $reservedPrice = $this->assertSlotIsAvailable(
                $service,
                $lockedTherapist,
                $validated['appointment_date'],
                $validated['appointment_time'],
            );

            Appointment::create([
                'customer_id' => $customer->id,
                'therapist_id' => $lockedTherapist->id,
                'service_id' => $service->id,
                'user_id' => auth()->id(),
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'reserved_price' => $reservedPrice,
                'appointment_status' => 'pending',
            ]);

            return redirect()
                ->route('appointments', ['date' => $validated['appointment_date']])
                ->with('success', 'Appointment created successfully.');
        });
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load(['customer', 'service', 'therapist']);
        $appointmentDate = Carbon::parse($appointment->appointment_date);
        $appointmentStart = Carbon::parse($appointment->appointment_date.' '.$appointment->appointment_time);
        $isReturningCustomer = $appointment->customer->transactions()
            ->where('payment_status', 'paid')
            ->exists();
        $statusLabels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'arrived' => 'Arrived',
            'in_service' => 'In Service',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return response()->json([
            'id' => $appointment->id,
            'customer_name' => $appointment->customer->customer_name,
            'contact_number' => $appointment->customer->contact_number,
            'is_returning_customer' => $isReturningCustomer,
            'customer_type' => $isReturningCustomer ? 'Returning Customer' : 'New Customer',
            'appointment_date' => $appointmentDate->toDateString(),
            'date_label' => $appointmentDate->format('M j, Y (D)'),
            'appointment_time' => Carbon::parse($appointment->appointment_time)->format('H:i'),
            'time_label' => $appointmentStart->format('g:i A'),
            'end_time' => $this->scheduler->endTime(
                $appointment->service,
                $appointmentDate->toDateString(),
                Carbon::parse($appointment->appointment_time)->format('H:i'),
            )->format('g:i A'),
            'service_name' => $appointment->service->service_name,
            'service_duration' => (int) $appointment->service->duration,
            'therapist_name' => $appointment->therapist->therapist_name,
            'status' => $appointment->appointment_status,
            'status_label' => $statusLabels[$appointment->appointment_status],
            'reserved_price' => (float) $appointment->reserved_price,
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate($this->appointmentRules(), $this->appointmentMessages());

        $updated = DB::transaction(function () use ($validated, $appointment): bool {
            $lockedAppointment = Appointment::query()
                ->lockForUpdate()
                ->findOrFail($appointment->id);

            if (! in_array($lockedAppointment->appointment_status, ['pending', 'confirmed'], true)) {
                return false;
            }

            $lockedTherapist = Therapist::query()
                ->whereKey($validated['therapist_id'])
                ->lockForUpdate()
                ->firstOrFail();
            $service = Service::query()->findOrFail($validated['service_id']);
            $customer = $this->customerFromValidatedInput($validated);
            $reservedPrice = $this->assertSlotIsAvailable(
                $service,
                $lockedTherapist,
                $validated['appointment_date'],
                $validated['appointment_time'],
                $lockedAppointment->id,
            );

            $lockedAppointment->update([
                'customer_id' => $customer->id,
                'therapist_id' => $lockedTherapist->id,
                'service_id' => $service->id,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'reserved_price' => $reservedPrice,
            ]);

            return true;
        });

        if (! $updated) {
            return back()->with('error', 'This appointment can no longer be edited.');
        }

        return redirect()
            ->route('appointments', ['date' => $validated['appointment_date']])
            ->with('success', 'Appointment updated successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $requestedStatus = $request->input('status');
        $allowedStatuses = ['pending', 'confirmed', 'arrived', 'in_service', 'completed', 'cancelled'];

        if (! is_string($requestedStatus) || ! in_array($requestedStatus, $allowedStatuses, true)) {
            return back()->with('error', 'Invalid status change.');
        }

        $appointmentDate = DB::transaction(function () use ($appointment, $requestedStatus): ?string {
            $lockedAppointment = Appointment::query()
                ->lockForUpdate()
                ->findOrFail($appointment->id);
            $transitions = [
                'pending' => ['confirmed', 'arrived', 'cancelled'],
                'confirmed' => ['arrived', 'cancelled'],
                'arrived' => ['in_service', 'cancelled'],
                'in_service' => ['completed'],
                'completed' => [],
                'cancelled' => [],
            ];

            if (! in_array($requestedStatus, $transitions[$lockedAppointment->appointment_status] ?? [], true)) {
                return null;
            }

            $therapist = Therapist::query()
                ->whereKey($lockedAppointment->therapist_id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedAppointment->update(['appointment_status' => $requestedStatus]);

            if ($requestedStatus === 'in_service') {
                $therapist->update(['availability_status' => 'in_service']);
            } elseif (in_array($requestedStatus, ['completed', 'cancelled'], true)
                && $therapist->availability_status === 'in_service') {
                $therapist->update(['availability_status' => 'available']);
            }

            return $lockedAppointment->appointment_date;
        });

        if ($appointmentDate === null) {
            return back()->with('error', 'Invalid status change.');
        }

        return redirect()
            ->route('appointments', ['date' => $appointmentDate])
            ->with('success', 'Appointment status updated successfully.');
    }

    public function availableTherapists(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
        ]);
        $service = Service::query()->findOrFail($validated['service_id']);
        $therapists = $this->scheduler->availableTherapists(
            $service,
            $validated['appointment_date'],
            $validated['appointment_time'],
            isset($validated['appointment_id']) ? (int) $validated['appointment_id'] : null,
        );

        return response()->json(
            $therapists->map(fn (Therapist $therapist): array => [
                'id' => $therapist->id,
                'name' => $therapist->therapist_name,
            ])->values()
        );
    }

    /**
     * @return array<string, array<int, string|object>>
     */
    private function appointmentRules(): array
    {
        return [
            'customer_id' => ['required_without:new_customer_name', 'integer', 'exists:customers,id'],
            'new_customer_name' => ['required_without:customer_id', 'string', 'max:100'],
            'new_customer_contact' => ['nullable', 'string', Rule::regex('/^(09|\+639)\d{9}$/')],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function appointmentMessages(): array
    {
        return [
            'new_customer_contact.regex' => 'Enter a valid PH mobile number, e.g. 09171234567.',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function customerFromValidatedInput(array $validated): Customer
    {
        if (filled($validated['new_customer_name'] ?? null)) {
            return Customer::create([
                'customer_name' => $validated['new_customer_name'],
                'contact_number' => $validated['new_customer_contact'] ?? null,
            ]);
        }

        return Customer::query()->findOrFail($validated['customer_id']);
    }

    private function assertSlotIsAvailable(
        Service $service,
        Therapist $therapist,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null,
    ): float {
        $start = Carbon::parse($date.' '.$time);
        $end = $this->scheduler->endTime($service, $date, $time);

        if ($start->lt(now())) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The appointment date and time cannot be in the past.',
            ]);
        }

        if ($start->format('H:i') < '11:00') {
            throw ValidationException::withMessages([
                'appointment_time' => 'Appointments must start at or after 11:00 AM.',
            ]);
        }

        if ($end->gt(Carbon::parse($date.' 23:00:00'))) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Appointments must end by 11:00 PM.',
            ]);
        }

        $price = $this->scheduler->priceFor($service, $time);

        if ($price === null) {
            throw ValidationException::withMessages([
                'service_id' => "{$service->service_name} is not offered at this time.",
            ]);
        }

        if (! $this->scheduler->therapistIsFree($therapist, $date, $start, $end, $ignoreAppointmentId)) {
            throw ValidationException::withMessages([
                'therapist_id' => 'This therapist is not available at the selected date and time.',
            ]);
        }

        return $price;
    }
}
