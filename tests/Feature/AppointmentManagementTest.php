<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_page_filters_real_records_and_displays_the_upcoming_reminder(): void
    {
        $this->travelTo('2026-11-27 10:30:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $maria = Customer::create(['customer_name' => 'Maria Santos', 'contact_number' => '09171234567']);
        $otherCustomer = Customer::create(['customer_name' => 'John Rivera', 'contact_number' => '09175550142']);
        $service = $this->createService();
        $therapist = $this->createTherapist();

        $pending = $this->createAppointment($maria, $therapist, $service, $cashier, '2026-11-27', '11:00:00', 'pending');
        $confirmed = $this->createAppointment($maria, $therapist, $service, $cashier, '2026-11-27', '11:30:00', 'confirmed');
        $arrived = $this->createAppointment($maria, $therapist, $service, $cashier, '2026-11-27', '12:00:00', 'arrived');
        $this->createAppointment($otherCustomer, $therapist, $service, $cashier, '2026-11-27', '12:30:00', 'pending');
        $this->createAppointment($maria, $therapist, $service, $cashier, '2026-11-26', '13:00:00', 'pending');

        $response = $this->actingAs($cashier)->get(route('appointments', [
            'date' => '2026-11-27',
            'status' => 'upcoming',
            'search' => 'Maria',
        ]));

        $response->assertOk()
            ->assertViewIs('appointments')
            ->assertSee("Today's Appointments")
            ->assertSee('Maria Santos')
            ->assertSee('Nov 27, 2026')
            ->assertSee('In 30 mins')
            ->assertDontSee('Ana Reyes')
            ->assertSee('date=2026-11-27', false)
            ->assertSee('status=upcoming', false)
            ->assertSee('search=Maria', false);

        $this->assertSame(
            [$pending->id, $confirmed->id],
            $response->viewData('appointments')->modelKeys(),
        );
        $this->assertSame($pending->id, $response->viewData('upcomingReminder')->id);
        $this->assertSame(30, $response->viewData('minutesUntilReminder'));
        $this->assertTrue($response->viewData('customers')->contains('id', $maria->id));
        $this->assertTrue($response->viewData('services')->contains('id', $service->id));
        $this->assertTrue($response->viewData('therapists')->contains('id', $therapist->id));
        $this->assertModelExists($arrived);
    }

    public function test_appointment_page_uses_today_by_default_and_labels_other_dates(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);

        $this->actingAs($cashier)->get(route('appointments'))
            ->assertOk()
            ->assertSee("Today's Appointments");

        $this->actingAs($cashier)->get(route('appointments', ['date' => '2026-11-25']))
            ->assertOk()
            ->assertSee('Appointments (Nov 25, 2026)')
            ->assertSee('No appointments for this date.');
    }

    public function test_appointment_details_report_customer_history_and_calculated_end_time(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $returningCustomer = Customer::create([
            'customer_name' => 'Returning Customer',
            'contact_number' => '09171234567',
        ]);
        $newCustomer = Customer::create([
            'customer_name' => 'New Customer',
            'contact_number' => null,
        ]);
        Transaction::create([
            'customer_id' => $returningCustomer->id,
            'user_id' => $cashier->id,
            'transaction_date' => '2026-11-26 12:00:00',
            'total_amount' => 350,
            'amount_paid' => 350,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);
        Transaction::create([
            'customer_id' => $newCustomer->id,
            'user_id' => $cashier->id,
            'transaction_date' => '2026-11-26 13:00:00',
            'total_amount' => 350,
            'amount_paid' => 350,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ]);
        $service = $this->createService(duration: 90);
        $therapist = $this->createTherapist();
        $returningAppointment = $this->createAppointment(
            $returningCustomer,
            $therapist,
            $service,
            $cashier,
            '2026-11-27',
            '11:30:00',
            'confirmed',
        );
        $newAppointment = $this->createAppointment(
            $newCustomer,
            $therapist,
            $service,
            $cashier,
            '2026-11-27',
            '13:00:00',
            'pending',
        );

        $this->actingAs($cashier)->get(route('appointments.show', $returningAppointment))
            ->assertOk()
            ->assertJsonPath('customer_name', 'Returning Customer')
            ->assertJsonPath('contact_number', '09171234567')
            ->assertJsonPath('is_returning_customer', true)
            ->assertJsonPath('customer_type', 'Returning Customer')
            ->assertJsonPath('date_label', 'Nov 27, 2026 (Fri)')
            ->assertJsonPath('time_label', '11:30 AM')
            ->assertJsonPath('end_time', '1:00 PM')
            ->assertJsonPath('service_name', $service->service_name)
            ->assertJsonPath('service_duration', 90)
            ->assertJsonPath('therapist_name', $therapist->therapist_name)
            ->assertJsonPath('status', 'confirmed')
            ->assertJsonPath('reserved_price', 350);

        $this->actingAs($cashier)->get(route('appointments.show', $newAppointment))
            ->assertOk()
            ->assertJsonPath('is_returning_customer', false)
            ->assertJsonPath('customer_type', 'New Customer');
    }

    public function test_cashier_can_create_and_update_an_appointment_with_recalculated_price(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $service = $this->createService();
        $therapist = $this->createTherapist();
        $this->createSchedule($therapist, '2026-11-27');

        $this->actingAs($cashier)->post(route('appointments.store'), [
            'new_customer_name' => 'New Booking Customer',
            'new_customer_contact' => '09171234567',
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments', ['date' => '2026-11-27']))
            ->assertSessionHas('success', 'Appointment created successfully.');

        $customer = Customer::query()->where('customer_name', 'New Booking Customer')->firstOrFail();
        $appointment = Appointment::query()->where('customer_id', $customer->id)->firstOrFail();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'customer_id' => $customer->id,
            'therapist_id' => $therapist->id,
            'user_id' => $cashier->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00',
            'reserved_price' => 300,
            'appointment_status' => 'pending',
        ]);

        $this->actingAs($cashier)->put(route('appointments.update', $appointment), [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments', ['date' => '2026-11-27']))
            ->assertSessionHas('success', 'Appointment updated successfully.');

        $this->actingAs($cashier)->put(route('appointments.update', $appointment), [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '16:00',
        ])->assertRedirect(route('appointments', ['date' => '2026-11-27']))
            ->assertSessionHas('success', 'Appointment updated successfully.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_time' => '16:00',
            'reserved_price' => 350,
            'appointment_status' => 'pending',
        ]);
    }

    public function test_booking_rejects_past_times_and_times_outside_spa_hours(): void
    {
        $this->travelTo('2026-11-27 10:30:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Booking Customer']);
        $service = $this->createService(duration: 90);
        $therapist = $this->createTherapist();
        $this->createSchedule($therapist, '2026-11-27');
        $this->createSchedule($therapist, '2026-11-28');
        $baseInput = [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
        ];

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            ...$baseInput,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '10:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'appointment_time' => 'The appointment date and time cannot be in the past.',
            ]);

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            ...$baseInput,
            'appointment_date' => '2026-11-28',
            'appointment_time' => '10:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'appointment_time' => 'Appointments must start at or after 11:00 AM.',
            ]);

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            ...$baseInput,
            'appointment_date' => '2026-11-28',
            'appointment_time' => '22:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'appointment_time' => 'Appointments must end by 11:00 PM.',
            ]);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_new_customer_contact_validation_uses_the_friendly_ph_mobile_message(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $service = $this->createService();
        $therapist = $this->createTherapist();

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            'new_customer_name' => 'Invalid Contact Customer',
            'new_customer_contact' => '12345',
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-28',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'new_customer_contact' => 'Enter a valid PH mobile number, e.g. 09171234567.',
            ]);

        $this->assertDatabaseMissing('customers', ['customer_name' => 'Invalid Contact Customer']);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_appointment_update_is_rejected_after_the_status_is_arrived(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Arrived Customer']);
        $service = $this->createService();
        $therapist = $this->createTherapist();
        $appointment = $this->createAppointment(
            $customer,
            $therapist,
            $service,
            $cashier,
            '2026-11-27',
            '11:00:00',
            'arrived',
        );

        $this->actingAs($cashier)->from(route('appointments'))->put(route('appointments.update', $appointment), [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-28',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHas('error', 'This appointment can no longer be edited.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00:00',
            'appointment_status' => 'arrived',
        ]);
    }

    public function test_booking_rolls_back_a_new_customer_when_service_is_not_offered(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $service = $this->createService(happyHourPrice: null);
        $therapist = $this->createTherapist();
        $this->createSchedule($therapist, '2026-11-27');

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            'new_customer_name' => 'Must Roll Back',
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'service_id' => 'Swedish Massage is not offered at this time.',
            ]);

        $this->assertDatabaseMissing('customers', ['customer_name' => 'Must Roll Back']);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_booking_rejects_a_therapist_with_an_overlapping_non_cancelled_appointment(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Existing Customer']);
        $service = $this->createService();
        $therapist = $this->createTherapist();
        $this->createSchedule($therapist, '2026-11-27');
        $this->createAppointment($customer, $therapist, $service, $cashier, '2026-11-27', '11:30:00', 'pending');

        $this->actingAs($cashier)->from(route('appointments'))->post(route('appointments.store'), [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '11:00',
        ])->assertRedirect(route('appointments'))
            ->assertSessionHasErrors([
                'therapist_id' => 'This therapist is not available at the selected date and time.',
            ]);

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_status_changes_follow_transitions_and_update_therapist_availability(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Status Customer']);
        $service = $this->createService();
        $therapist = $this->createTherapist();
        $appointment = $this->createAppointment($customer, $therapist, $service, $cashier, '2026-11-27', '11:00:00', 'pending');
        $statusRoute = route('appointments.status', $appointment);

        $this->actingAs($cashier)->patch($statusRoute, ['status' => 'confirmed'])
            ->assertRedirect(route('appointments', ['date' => '2026-11-27']))
            ->assertSessionHas('success', 'Appointment status updated successfully.');
        $this->patch($statusRoute, ['status' => 'arrived'])
            ->assertRedirect(route('appointments', ['date' => '2026-11-27']));
        $this->patch($statusRoute, ['status' => 'in_service'])
            ->assertRedirect(route('appointments', ['date' => '2026-11-27']));

        $therapist->refresh();
        $this->assertSame('in_service', $therapist->availability_status);

        $this->patch($statusRoute, ['status' => 'completed'])
            ->assertRedirect(route('appointments', ['date' => '2026-11-27']));

        $appointment->refresh();
        $therapist->refresh();
        $this->assertSame('completed', $appointment->appointment_status);
        $this->assertSame('available', $therapist->availability_status);

        $this->from(route('appointments'))->patch($statusRoute, ['status' => 'cancelled'])
            ->assertRedirect(route('appointments'))
            ->assertSessionHas('error', 'Invalid status change.');
        $this->from(route('appointments'))->patch($statusRoute, ['status' => 'unexpected'])
            ->assertRedirect(route('appointments'))
            ->assertSessionHas('error', 'Invalid status change.');
    }

    public function test_cancelling_an_arrived_appointment_releases_a_therapist_who_is_in_service(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Cancellation Customer']);
        $service = $this->createService();
        $therapist = $this->createTherapist(['availability_status' => 'in_service']);
        $appointment = $this->createAppointment($customer, $therapist, $service, $cashier, '2026-11-27', '11:00:00', 'arrived');

        $this->actingAs($cashier)->patch(route('appointments.status', $appointment), ['status' => 'cancelled'])
            ->assertRedirect(route('appointments', ['date' => '2026-11-27']))
            ->assertSessionHas('success', 'Appointment status updated successfully.');

        $therapist->refresh();
        $appointment->refresh();
        $this->assertSame('cancelled', $appointment->appointment_status);
        $this->assertSame('available', $therapist->availability_status);
    }

    public function test_available_therapists_endpoint_returns_scheduler_results(): void
    {
        $this->travelTo('2026-11-27 09:00:00');
        $cashier = User::factory()->create(['role' => 'cashier']);
        $service = $this->createService();
        $therapist = $this->createTherapist();
        $this->createSchedule($therapist, '2026-11-28');

        $this->actingAs($cashier)->get(route('appointments.available-therapists', [
            'service_id' => $service->id,
            'appointment_date' => '2026-11-28',
            'appointment_time' => '11:00',
        ]))->assertOk()
            ->assertExactJson([['id' => $therapist->id, 'name' => $therapist->therapist_name]]);
    }

    private function createService(
        int $duration = 60,
        ?float $happyHourPrice = 300,
        ?float $regularPrice = 350,
    ): Service {
        return Service::create([
            'service_name' => 'Swedish Massage',
            'duration' => $duration,
            'happy_hour_price' => $happyHourPrice,
            'regular_price' => $regularPrice,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createTherapist(array $attributes = []): Therapist
    {
        return Therapist::create(array_merge([
            'therapist_name' => 'Therapist A',
            'availability_status' => 'available',
        ], $attributes));
    }

    private function createSchedule(Therapist $therapist, string $date): TherapistSchedule
    {
        return TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => $date,
            'start_time' => '11:00:00',
            'end_time' => '23:00:00',
        ]);
    }

    private function createAppointment(
        Customer $customer,
        Therapist $therapist,
        Service $service,
        User $user,
        string $date,
        string $time,
        string $status,
    ): Appointment {
        return Appointment::create([
            'customer_id' => $customer->id,
            'therapist_id' => $therapist->id,
            'service_id' => $service->id,
            'user_id' => $user->id,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'reserved_price' => $service->regular_price ?? $service->happy_hour_price,
            'appointment_status' => $status,
        ]);
    }
}
