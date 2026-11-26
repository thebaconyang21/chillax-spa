<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Appointment;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Transaction;
use App\Services\AppointmentScheduler;
use App\Services\PosCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request, AppointmentScheduler $scheduler): View|RedirectResponse
    {
        $appointment = null;
        if ($request->filled('appointment')) {
            $appointment = Appointment::query()->with(['customer', 'service', 'therapist', 'transaction'])
                ->find($request->integer('appointment'));
            if (! $appointment) {
                return redirect()->route('appointments')->with('error', 'This appointment was cancelled.');
            }
            if ($appointment->transaction) {
                return redirect()->route('appointments')->with('error', 'This appointment is already paid.');
            }
            if (in_array($appointment->appointment_status, ['pending', 'confirmed'], true)) {
                return redirect()->route('appointments')->with('error', 'Mark the appointment as Arrived first.');
            }
            if (! in_array($appointment->appointment_status, ['arrived', 'in_service'], true)) {
                $message = $appointment->appointment_status === 'cancelled' ? 'This appointment was cancelled.' : 'Mark the appointment as Arrived first.';

                return redirect()->route('appointments')->with('error', $message);
            }
        }

        $now = Carbon::now('Asia/Manila');
        $happyStart = (string) config('spa.happy_hour_start');
        $happyEnd = (string) config('spa.happy_hour_end');
        $services = Service::query()->orderBy('service_name')->get()->map(function (Service $service) use ($scheduler, $now): Service {
            $service->current_price = $scheduler->priceFor($service, $now->format('H:i'));

            return $service;
        });

        return view('pos', [
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'services' => $services,
            'addons' => Addon::query()->orderBy('addon_name')->get(),
            'therapists' => Therapist::query()->where('availability_status', 'available')->orderBy('therapist_name')->get(),
            'appointment' => $appointment,
            'happyHourActive' => $now->format('H:i') >= $happyStart && $now->format('H:i') < $happyEnd,
            'happyHourStart' => Carbon::createFromFormat('H:i', $happyStart, 'Asia/Manila')->format('g:i A'),
            'happyHourEnd' => Carbon::createFromFormat('H:i', $happyEnd, 'Asia/Manila')->format('g:i A'),
        ]);
    }

    public function store(Request $request, PosCalculator $calculator): RedirectResponse
    {
        $validated = $request->validate([
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
            'customer_id' => ['required_without:new_customer_name', 'nullable', 'integer', 'exists:customers,id'],
            'new_customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:100'],
            'new_customer_contact' => ['nullable', 'string', 'max:30'],
            'payment_method' => ['required', 'in:cash,ecash'],
            'amount_paid' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'lines.*.therapist_id' => ['required', 'integer', 'exists:therapists,id'],
            'lines.*.addons' => ['nullable', 'array'],
            'lines.*.addons.*' => ['integer', 'exists:addons,id'],
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated, $calculator): Transaction {
                $appointment = null;
                if (! empty($validated['appointment_id'])) {
                    $appointment = Appointment::query()->lockForUpdate()->findOrFail($validated['appointment_id']);
                    if ($appointment->transaction()->exists()) {
                        throw ValidationException::withMessages(['appointment_id' => 'This appointment is already paid.']);
                    }
                    if (! in_array($appointment->appointment_status, ['arrived', 'in_service'], true)) {
                        throw ValidationException::withMessages(['appointment_id' => 'Mark the appointment as Arrived first.']);
                    }
                    if (count($validated['lines']) !== 1) {
                        throw ValidationException::withMessages(['lines' => 'An appointment payment must contain its reserved service only.']);
                    }
                }

                $customer = ! empty($validated['new_customer_name'])
                    ? Customer::create(['customer_name' => $validated['new_customer_name'], 'contact_number' => $validated['new_customer_contact'] ?? null])
                    : Customer::query()->findOrFail($validated['customer_id']);
                if ($appointment && (int) $customer->id !== (int) $appointment->customer_id) {
                    throw ValidationException::withMessages(['customer_id' => 'The customer must match the appointment.']);
                }

                $now = Carbon::now('Asia/Manila');
                $lineData = [];
                $total = 0.0;
                foreach (array_values($validated['lines']) as $line) {
                    $service = $appointment ? Service::query()->findOrFail($appointment->service_id) : Service::query()->findOrFail($line['service_id']);
                    $therapistId = $appointment ? (int) $appointment->therapist_id : (int) $line['therapist_id'];
                    if ($appointment && ((int) $line['service_id'] !== (int) $appointment->service_id || (int) $line['therapist_id'] !== $therapistId)) {
                        throw ValidationException::withMessages(['lines' => 'The service and therapist must match the appointment.']);
                    }
                    $therapist = Therapist::query()->lockForUpdate()->findOrFail($therapistId);
                    if (! $appointment) {
                        if ($therapist->availability_status === 'off_duty') {
                            throw ValidationException::withMessages(['lines' => "{$therapist->therapist_name} is off duty."]);
                        }
                        $end = $now->copy()->addMinutes((int) $service->duration);
                        $overlap = $therapist->appointments()
                            ->where('appointment_status', '!=', 'cancelled')
                            ->with('service')
                            ->get()
                            ->contains(function (Appointment $existing) use ($now, $end): bool {
                                $existingStart = Carbon::parse($existing->appointment_date.' '.$existing->appointment_time, 'Asia/Manila');
                                $existingEnd = $existingStart->copy()->addMinutes((int) $existing->service->duration);

                                return $existingStart->lt($end) && $existingEnd->gt($now);
                            });
                        if ($overlap) {
                            throw ValidationException::withMessages(['lines' => "{$therapist->therapist_name} has an overlapping appointment."]);
                        }
                    }
                    $calculated = $calculator->calculate($service, array_map('intval', $line['addons'] ?? []), $appointment !== null, $now->format('H:i'), $appointment ? (float) $appointment->reserved_price : null);
                    $lineData[] = ['service' => $service, 'therapist' => $therapist, ...$calculated];
                    $total += $calculated['subtotal'];
                }

                $total = round($total, 2);
                $paid = round((float) $validated['amount_paid'], 2);
                if ($paid < $total) {
                    throw ValidationException::withMessages(['amount_paid' => 'Amount paid is less than the total.']);
                }
                if ($validated['payment_method'] === 'ecash' && $paid !== $total) {
                    throw ValidationException::withMessages(['amount_paid' => 'E-cash amount paid must equal the total.']);
                }
                $change = $validated['payment_method'] === 'cash' ? round($paid - $total, 2) : 0.0;

                $transaction = Transaction::create([
                    'customer_id' => $customer->id,
                    'appointment_id' => $appointment?->id,
                    'user_id' => auth()->id(),
                    'transaction_date' => now(),
                    'total_amount' => $total,
                    'amount_paid' => $paid,
                    'change_amount' => $change,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'paid',
                ]);

                foreach ($lineData as $line) {
                    $detail = $transaction->details()->create([
                        'service_id' => $line['service']->id,
                        'therapist_id' => $line['therapist']->id,
                        'service_price' => $line['service_price'],
                        'quantity' => 1,
                        'subtotal' => $line['subtotal'],
                    ]);
                    foreach ($line['addons'] as $addonLine) {
                        $detail->addons()->create(['addon_id' => $addonLine['addon']->id, 'addon_price' => $addonLine['price']]);
                    }
                    Commission::create([
                        'therapist_id' => $line['therapist']->id,
                        'transaction_detail_id' => $detail->id,
                        'commission_amount' => $line['commission'],
                        'commission_date' => today(),
                    ]);
                }
                if ($appointment) {
                    $appointment->update(['appointment_status' => 'completed']);
                    $therapist = Therapist::query()->find($appointment->therapist_id);
                    if ($therapist?->availability_status === 'in_service') {
                        $therapist->update(['availability_status' => 'available']);
                    }
                }

                return $transaction;
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors(['pos' => 'The payment could not be processed. Please review the details and try again.']);
        }

        return redirect()->route('pos.receipt', $transaction);
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['customer', 'user', 'details.service', 'details.therapist', 'details.addons.addon']);

        return view('pos-receipt', compact('transaction'));
    }
}
