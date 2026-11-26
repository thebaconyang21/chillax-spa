<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapistManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_therapist_page_displays_weekly_paid_service_and_commission_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $therapist = Therapist::create([
            'therapist_name' => 'Therapist A',
            'availability_status' => 'available',
        ]);
        Therapist::create([
            'therapist_name' => 'Therapist B',
            'availability_status' => 'off_duty',
        ]);
        $customer = Customer::create(['customer_name' => 'Test Customer']);
        $service = Service::create([
            'service_name' => 'Swedish Massage',
            'duration' => 60,
            'regular_price' => 600,
        ]);

        $paidTransaction = $this->createTransaction($customer, $admin, '2026-11-18 10:00:00', 'paid', 600);
        $paidDetail = $this->createDetail($paidTransaction, $service, $therapist, 600);
        Commission::create([
            'therapist_id' => $therapist->id,
            'transaction_detail_id' => $paidDetail->id,
            'commission_amount' => 300,
            'commission_date' => '2026-11-20',
        ]);

        $unpaidTransaction = $this->createTransaction($customer, $admin, '2026-11-19 10:00:00', 'unpaid', 1000);
        $this->createDetail($unpaidTransaction, $service, $therapist, 1000);

        $previousWeekTransaction = $this->createTransaction($customer, $admin, '2026-11-15 10:00:00', 'paid', 900);
        $previousWeekDetail = $this->createDetail($previousWeekTransaction, $service, $therapist, 900);
        Commission::create([
            'therapist_id' => $therapist->id,
            'transaction_detail_id' => $previousWeekDetail->id,
            'commission_amount' => 450,
            'commission_date' => '2026-11-15',
        ]);

        $response = $this->actingAs($admin)->get(route('therapists', [
            'search' => 'Therapist A',
            'week_start' => '2026-11-16',
        ]));

        $response->assertOk()
            ->assertViewIs('therapists')
            ->assertSee('Therapist & Commission')
            ->assertSee('Therapist A')
            ->assertDontSee('Therapist B')
            ->assertSee('Nov 16 - Nov 22, 2026')
            ->assertSee('₱600.00')
            ->assertSee('₱300.00')
            ->assertDontSee('₱1,000.00')
            ->assertDontSee('₱450.00');

        $displayedTherapist = $response->viewData('therapists')->firstWhere('id', $therapist->id);
        $this->assertSame(1, (int) $displayedTherapist->completed_services);
        $this->assertSame(600.0, (float) $displayedTherapist->total_sales);
        $this->assertSame(300.0, (float) $displayedTherapist->commission);
        $this->assertSame(2, $response->viewData('totalTherapists'));
        $this->assertSame(1, $response->viewData('availableTherapists'));
        $this->assertSame(300.0, (float) $response->viewData('totalCommission'));
    }

    public function test_therapist_profile_returns_upcoming_schedules_and_latest_ten_paid_services(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $therapist = Therapist::create([
            'therapist_name' => 'Therapist Profile',
            'contact_number' => '09171234567',
            'availability_status' => 'in_service',
        ]);
        $customer = Customer::create(['customer_name' => 'Maria Santos']);
        $service = Service::create([
            'service_name' => 'Swedish Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);

        TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => '2026-11-26',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);
        TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => '2026-11-27',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
        TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => '2026-11-28',
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        for ($day = 1; $day <= 11; $day++) {
            $transaction = $this->createTransaction(
                $customer,
                $admin,
                sprintf('2026-11-%02d 12:00:00', $day),
                'paid',
                350 + $day,
            );
            $detail = $this->createDetail($transaction, $service, $therapist, 350 + $day);

            if ($day === 11) {
                Commission::create([
                    'therapist_id' => $therapist->id,
                    'transaction_detail_id' => $detail->id,
                    'commission_amount' => 36,
                    'commission_date' => '2026-11-11',
                ]);
            }
        }

        $response = $this->actingAs($admin)->get(route('therapists.show', $therapist));

        $response->assertOk()
            ->assertJsonPath('therapist.therapist_name', 'Therapist Profile')
            ->assertJsonPath('therapist.contact_number', '09171234567')
            ->assertJsonPath('schedules.0.schedule_date', '2026-11-27')
            ->assertJsonPath('schedules.0.start_label', '10:00 AM')
            ->assertJsonPath('completed_services.0.date', 'Nov 11, 2026')
            ->assertJsonPath('completed_services.0.service_name', 'Swedish Massage')
            ->assertJsonPath('completed_services.0.customer_name', 'Maria Santos')
            ->assertJsonPath('completed_services.0.commission', 36);
        $response->assertJsonCount(2, 'schedules')
            ->assertJsonCount(10, 'completed_services');
    }

    public function test_admin_can_create_update_and_delete_a_therapist_and_their_schedules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('therapists.store'), [
            'therapist_name' => 'New Therapist',
            'contact_number' => '09171234567',
            'availability_status' => 'available',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Therapist added successfully.');

        $therapist = Therapist::query()->where('therapist_name', 'New Therapist')->firstOrFail();
        $this->assertModelExists($therapist);

        $this->actingAs($admin)->put(route('therapists.update', $therapist), [
            'therapist_name' => 'Updated Therapist',
            'contact_number' => null,
            'availability_status' => 'off_duty',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Therapist updated successfully.');

        $this->assertDatabaseHas('therapists', [
            'id' => $therapist->id,
            'therapist_name' => 'Updated Therapist',
            'contact_number' => null,
            'availability_status' => 'off_duty',
        ]);

        $schedule = TherapistSchedule::create([
            'therapist_id' => $therapist->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);

        $this->actingAs($admin)->delete(route('therapists.destroy', $therapist))
            ->assertRedirect(route('therapists'))
            ->assertSessionHas('success', 'Therapist deleted successfully.');

        $this->assertDatabaseMissing('therapists', ['id' => $therapist->id]);
        $this->assertDatabaseMissing('therapist_schedules', ['id' => $schedule->id]);
    }

    public function test_therapist_contact_number_rejects_invalid_ph_mobile_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->from(route('therapists'))->post(route('therapists.store'), [
            'therapist_name' => 'Invalid Phone Therapist',
            'contact_number' => '0917 123 4567',
            'availability_status' => 'available',
        ])->assertRedirect(route('therapists'))
            ->assertSessionHasErrors([
                'contact_number' => 'Enter a valid PH mobile number, e.g. 09171234567.',
            ]);

        $this->assertDatabaseMissing('therapists', ['therapist_name' => 'Invalid Phone Therapist']);
    }

    public function test_therapist_with_any_existing_record_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['customer_name' => 'Existing Customer']);
        $service = Service::create([
            'service_name' => 'Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $appointmentTherapist = Therapist::create(['therapist_name' => 'Appointment Therapist']);
        $detailTherapist = Therapist::create(['therapist_name' => 'Detail Therapist']);
        $commissionTherapist = Therapist::create(['therapist_name' => 'Commission Therapist']);
        $transaction = $this->createTransaction($customer, $admin, '2026-11-27 10:00:00', 'paid', 350);
        $detail = $this->createDetail($transaction, $service, $detailTherapist, 350);

        Appointment::create([
            'customer_id' => $customer->id,
            'therapist_id' => $appointmentTherapist->id,
            'service_id' => $service->id,
            'user_id' => $admin->id,
            'appointment_date' => '2026-11-27',
            'appointment_time' => '10:00:00',
            'reserved_price' => 350,
        ]);
        Commission::create([
            'therapist_id' => $commissionTherapist->id,
            'transaction_detail_id' => $detail->id,
            'commission_amount' => 175,
            'commission_date' => '2026-11-27',
        ]);

        foreach ([$appointmentTherapist, $detailTherapist, $commissionTherapist] as $therapist) {
            $this->actingAs($admin)->delete(route('therapists.destroy', $therapist))
                ->assertRedirect(route('therapists'))
                ->assertSessionHas('error', 'This therapist has existing records. Set them to Off Duty instead.');

            $this->assertModelExists($therapist);
        }
    }

    private function createTransaction(
        Customer $customer,
        User $user,
        string $date,
        string $status,
        float $amount,
    ): Transaction {
        return Transaction::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'transaction_date' => $date,
            'total_amount' => $amount,
            'amount_paid' => $amount,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => $status,
        ]);
    }

    private function createDetail(
        Transaction $transaction,
        Service $service,
        Therapist $therapist,
        float $amount,
    ): TransactionDetail {
        return TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'service_price' => $amount,
            'quantity' => 1,
            'subtotal' => $amount,
        ]);
    }
}
