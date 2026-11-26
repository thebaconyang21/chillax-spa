<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class AppointmentScheduler
{
    public function endTime(Service $service, string $date, string $time): Carbon
    {
        return Carbon::parse($date.' '.$time)->addMinutes((int) $service->duration);
    }

    public function priceFor(Service $service, string $time): ?float
    {
        $time = Carbon::parse($time)->format('H:i');
        $isHappyHour = $time >= config('spa.happy_hour_start')
            && $time < config('spa.happy_hour_end');
        $price = $isHappyHour ? $service->happy_hour_price : $service->regular_price;

        return $price === null ? null : (float) $price;
    }

    public function therapistIsFree(
        Therapist $therapist,
        string $date,
        Carbon $start,
        Carbon $end,
        ?int $ignoreAppointmentId = null,
    ): bool {
        $date = Carbon::parse($date)->toDateString();
        $schedules = $therapist->relationLoaded('schedules')
            ? $therapist->schedules
            : $therapist->schedules()->whereDate('schedule_date', $date)->get();
        $fitsSchedule = $schedules->contains(function (TherapistSchedule $schedule) use ($date, $start, $end): bool {
            if (Carbon::parse($schedule->schedule_date)->toDateString() !== $date) {
                return false;
            }

            $scheduleStart = Carbon::parse($date.' '.$schedule->start_time);
            $scheduleEnd = Carbon::parse($date.' '.$schedule->end_time);

            return $scheduleStart->lte($start) && $scheduleEnd->gte($end);
        });

        if (! $fitsSchedule) {
            return false;
        }

        $appointments = $therapist->relationLoaded('appointments')
            ? $therapist->appointments
            : $therapist->appointments()
                ->whereDate('appointment_date', $date)
                ->where('appointment_status', '!=', 'cancelled')
                ->with('service')
                ->get();
        $appointments->loadMissing('service');

        foreach ($appointments as $appointment) {
            if ($appointment->id === $ignoreAppointmentId
                || $appointment->appointment_status === 'cancelled'
                || Carbon::parse($appointment->appointment_date)->toDateString() !== $date) {
                continue;
            }

            $existingStart = Carbon::parse($date.' '.$appointment->appointment_time);
            $existingEnd = $existingStart->copy()->addMinutes((int) $appointment->service->duration);

            if ($existingStart->lt($end) && $existingEnd->gt($start)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Collection<int, Therapist>
     */
    public function availableTherapists(
        Service $service,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null,
    ): Collection {
        $date = Carbon::parse($date)->toDateString();
        $start = Carbon::parse($date.' '.$time);
        $end = $this->endTime($service, $date, $time);

        $therapists = Therapist::query()
            ->when($date === now()->toDateString(), function (Builder $query): void {
                $query->where('availability_status', '!=', 'off_duty');
            })
            ->with([
                'schedules' => static function (HasMany $query) use ($date): void {
                    $query->whereDate('schedule_date', $date);
                },
                'appointments' => static function (HasMany $query) use ($date): void {
                    $query->whereDate('appointment_date', $date)
                        ->where('appointment_status', '!=', 'cancelled')
                        ->with('service');
                },
            ])
            ->get();

        return $therapists
            ->filter(fn (Therapist $therapist): bool => $this->therapistIsFree(
                $therapist,
                $date,
                $start,
                $end,
                $ignoreAppointmentId,
            ))
            ->values();
    }
}
