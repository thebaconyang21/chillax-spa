<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_page_searches_and_paginates_database_records(): void
    {
        $admin = User::factory()->create(['role' => 'cashier']);

        for ($index = 1; $index <= 12; $index++) {
            Customer::create([
                'customer_name' => 'Searchable Customer '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'contact_number' => '09'.str_pad((string) (170000000 + $index), 9, '0', STR_PAD_LEFT),
            ]);
        }

        $response = $this->actingAs($admin)->get(route('customers', ['search' => 'Searchable']));

        $response->assertOk()
            ->assertViewIs('customers')
            ->assertSee('Customer Management')
            ->assertSee('Searchable Customer 01')
            ->assertSee('Searchable Customer 10')
            ->assertDontSee('Searchable Customer 11')
            ->assertSee('value="Searchable"', false)
            ->assertSee('search=Searchable', false)
            ->assertSee('page=2', false);

        $this->actingAs($admin)->get(route('customers', ['search' => '09170000001']))
            ->assertOk()
            ->assertSee('Searchable Customer 01')
            ->assertDontSee('Searchable Customer 02');
    }

    public function test_index_counts_only_paid_transactions_and_uses_the_latest_paid_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'customer_name' => 'Returning Customer',
            'contact_number' => '09171234567',
        ]);
        $this->createTransaction($customer, $admin, '2026-11-20 11:00:00', 'paid');
        $this->createTransaction($customer, $admin, '2026-11-25 12:00:00', 'paid');
        $this->createTransaction($customer, $admin, '2026-11-27 12:00:00', 'unpaid');

        $this->actingAs($admin)->get(route('customers'))
            ->assertOk()
            ->assertSee('Returning Customer')
            ->assertSee('2')
            ->assertSee('Nov 25, 2026')
            ->assertDontSee('Nov 27, 2026');
    }

    public function test_customer_details_return_latest_appointments_and_paid_transactions(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create([
            'customer_name' => 'Maria Santos',
            'contact_number' => '09171234567',
        ]);
        $service = Service::create([
            'service_name' => 'Swedish Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $therapist = Therapist::create(['therapist_name' => 'Therapist A']);

        for ($index = 1; $index <= 11; $index++) {
            $date = sprintf('2026-11-%02d', $index);

            Appointment::create([
                'customer_id' => $customer->id,
                'therapist_id' => $therapist->id,
                'service_id' => $service->id,
                'user_id' => $cashier->id,
                'appointment_date' => $date,
                'appointment_time' => '10:00:00',
                'reserved_price' => 350,
                'appointment_status' => 'completed',
            ]);

            $this->createTransaction($customer, $cashier, $date.' 11:00:00', 'paid', 350 + $index);
        }

        $this->createTransaction($customer, $cashier, '2026-11-27 12:00:00', 'unpaid', 999);

        $response = $this->actingAs($cashier)->get(route('customers.show', $customer));

        $response->assertOk()
            ->assertJsonPath('customer.customer_name', 'Maria Santos')
            ->assertJsonPath('customer.contact_number', '09171234567')
            ->assertJsonPath('total_visits', 11)
            ->assertJsonPath('last_visit', 'Nov 11, 2026')
            ->assertJsonPath('appointments.0.date', 'Nov 11, 2026')
            ->assertJsonPath('appointments.0.time', '10:00 AM')
            ->assertJsonPath('appointments.0.service_name', 'Swedish Massage')
            ->assertJsonPath('appointments.0.therapist_name', 'Therapist A')
            ->assertJsonPath('transactions.0.date', 'Nov 11, 2026')
            ->assertJsonPath('transactions.0.payment_method', 'cash');

        $this->assertSame(361, (int) $response->json('transactions.0.total_amount'));
        $this->assertCount(10, $response->json('appointments'));
        $this->assertCount(10, $response->json('transactions'));
    }

    public function test_admin_can_create_update_and_delete_a_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('customers.store'), [
            'customer_name' => 'New Customer',
            'contact_number' => '09171234567',
        ])->assertRedirect(route('customers'))
            ->assertSessionHas('success', 'Customer added successfully.');

        $customer = Customer::query()->where('customer_name', 'New Customer')->firstOrFail();
        $this->assertModelExists($customer);

        $this->actingAs($admin)->put(route('customers.update', $customer), [
            'customer_name' => 'Updated Customer',
            'contact_number' => '09171234567',
            'customer_id' => $customer->id,
        ])->assertRedirect(route('customers'))
            ->assertSessionHas('success', 'Customer updated successfully.');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'customer_name' => 'Updated Customer',
            'contact_number' => '09171234567',
        ]);

        $this->actingAs($admin)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers'))
            ->assertSessionHas('success', 'Customer deleted successfully.');

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_customer_contact_number_requires_a_valid_ph_mobile_number(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->from(route('customers'))
            ->post(route('customers.store'), [
                'customer_name' => 'Invalid Phone',
                'contact_number' => '0917 123 4567',
            ]);

        $response->assertRedirect(route('customers'))
            ->assertSessionHasErrors([
                'contact_number' => 'Enter a valid PH mobile number, e.g. 09171234567.',
            ]);

        $this->assertDatabaseMissing('customers', ['customer_name' => 'Invalid Phone']);
    }

    public function test_customer_contact_number_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Customer::create([
            'customer_name' => 'Existing Customer',
            'contact_number' => '09171234567',
        ]);

        $this->actingAs($admin)->from(route('customers'))->post(route('customers.store'), [
            'customer_name' => 'Duplicate Customer',
            'contact_number' => '09171234567',
        ])->assertRedirect(route('customers'))
            ->assertSessionHasErrors('contact_number');

        $this->assertDatabaseMissing('customers', ['customer_name' => 'Duplicate Customer']);
    }

    public function test_customer_cannot_be_deleted_when_it_has_an_appointment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['customer_name' => 'Booked Customer']);
        $service = Service::create([
            'service_name' => 'Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $therapist = Therapist::create(['therapist_name' => 'Therapist A']);

        Appointment::create([
            'customer_id' => $customer->id,
            'therapist_id' => $therapist->id,
            'service_id' => $service->id,
            'user_id' => $admin->id,
            'appointment_date' => '2026-11-20',
            'appointment_time' => '10:00:00',
            'reserved_price' => 350,
        ]);

        $this->actingAs($admin)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers'))
            ->assertSessionHas('error', 'This customer has existing records and cannot be deleted.');

        $this->assertModelExists($customer);
    }

    public function test_customer_cannot_be_deleted_when_it_has_a_transaction(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['customer_name' => 'Transaction Customer']);
        $this->createTransaction($customer, $admin, '2026-11-20 11:00:00', 'unpaid');

        $this->actingAs($admin)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers'))
            ->assertSessionHas('error', 'This customer has existing records and cannot be deleted.');

        $this->assertModelExists($customer);
    }

    private function createTransaction(
        Customer $customer,
        User $user,
        string $date,
        string $status,
        float $amount = 500,
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
}
