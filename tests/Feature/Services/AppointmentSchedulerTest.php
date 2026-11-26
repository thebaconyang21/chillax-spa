<?php

namespace Tests\Feature\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\User;
use App\Services\AppointmentScheduler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_end_time_and_happy_hour_price_use_duration_and_exclusive_end_time(): void
    {
        $service = Service::create([
            'service_name' => 'Swedish Massage',
            'duration' => 90,
            'happy_hour_price' => 300,
            'regular_price' => 350,
        ]);
        $scheduler = app(AppointmentScheduler::class);

        $this->assertSame(
            '2026-11-27 12:30:00',
            $scheduler->endTime($service, '2026-11-27', '11:00')->format('Y-m-d H:i:s'),
        );
        $this->assertSame(300.0, $scheduler->priceFor($service, '11:00'));
        $this->assertSame(300.0, $scheduler->priceFor($service, '15:59'));
        $this->assertSame(350.0, $scheduler->priceFor($service, '16:00'));

        $happyHourUnavailableService = Service::create([
            'service_name' => 'Regular Only Massage',
            'duration' => 60,
            'happy_hour_price' => null,
            'regular_price' => 400,
        ]);
        $this->assertNull($scheduler->priceFor($happyHourUnavailableService, '15:00'));
        $this->assertSame(400.0, $scheduler->priceFor($happyHourUnavailableService, '16:00'));
    }

    public function test_therapist_free_check_requires_full_schedule_and_non_overlapping_appointments(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $scheduler = app(AppointmentScheduler::class);
        $user = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Booked Customer']);
        $service = Service::create([
            'service_name' => 'Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $therapist = Therapist::create([
            'therapist_name' => 'Off Duty Therapist',
            'availability_status' => 'off_duty',
        ]);
        TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => '2026-11-27',
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
        ]);
        $start = now()->setTime(11, 30);
        $end = $start->copy()->addHour();

        $this->assertTrue($scheduler->therapistIsFree($therapist, '2026-11-27', $start, $end));

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'therapist_id' => $therapist->id,
            'service_id' => $service->id,
            'user_id' => $user->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '12:00:00',
            'reserved_price' => 350,
            'appointment_status' => 'pending',
        ]);

        $this->assertFalse($scheduler->therapistIsFree($therapist, '2026-11-27', $start, $end));
        $this->assertTrue($scheduler->therapistIsFree($therapist, '2026-11-27', $start, $end, $appointment->id));

        $appointment->update(['appointment_status' => 'cancelled']);
        $this->assertTrue($scheduler->therapistIsFree($therapist, '2026-11-27', $start, $end));

        $outsideScheduleTherapist = Therapist::create(['therapist_name' => 'Short Schedule Therapist']);
        TherapistSchedule::create([
            'therapist_id' => $outsideScheduleTherapist->id,
            'schedule_date' => '2026-11-27',
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        $this->assertFalse($scheduler->therapistIsFree($outsideScheduleTherapist, '2026-11-27', $start, $end));
    }

    public function test_available_therapists_excludes_today_off_duty_therapists_but_not_future_off_duty_therapists(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $scheduler = app(AppointmentScheduler::class);
        $service = Service::create([
            'service_name' => 'Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $availableTherapist = Therapist::create([
            'therapist_name' => 'Available Therapist',
            'availability_status' => 'available',
        ]);
        $offDutyTherapist = Therapist::create([
            'therapist_name' => 'Off Duty Therapist',
            'availability_status' => 'off_duty',
        ]);

        foreach ([$availableTherapist, $offDutyTherapist] as $therapist) {
            foreach (['2026-11-27', '2026-11-28'] as $date) {
                TherapistSchedule::create([
                    'therapist_id' => $therapist->id,
                    'schedule_date' => $date,
                    'start_time' => '11:00:00',
                    'end_time' => '23:00:00',
                ]);
            }
        }

        $todayTherapists = $scheduler->availableTherapists($service, '2026-11-27', '12:00');
        $this->assertSame([$availableTherapist->id], $todayTherapists->modelKeys());

        $futureTherapists = $scheduler->availableTherapists($service, '2026-11-28', '12:00');
        $this->assertSame(
            [$availableTherapist->id, $offDutyTherapist->id],
            $futureTherapists->modelKeys(),
        );
    }
}
