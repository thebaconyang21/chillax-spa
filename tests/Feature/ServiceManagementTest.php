<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_route_renders_database_services_and_addons_with_formatted_values(): void
    {
        Service::create([
            'service_name' => 'Database Massage',
            'duration' => 90,
            'happy_hour_price' => 300,
            'regular_price' => 450.5,
        ]);
        Service::create([
            'service_name' => 'Short Massage',
            'duration' => 45,
            'happy_hour_price' => null,
            'regular_price' => 250,
        ]);
        Addon::create(['addon_name' => 'Database Add-on', 'price' => 150]);

        $response = $this->actingAs($this->admin())->get('/services');

        $response->assertOk()
            ->assertViewIs('services')
            ->assertSee('Service Management')
            ->assertSee('Database Massage')
            ->assertSee('1.5 hrs')
            ->assertSee('45 mins')
            ->assertSee('₱300.00')
            ->assertSee('₱450.50')
            ->assertSee('Database Add-on')
            ->assertSee('₱150.00')
            ->assertSee('-', false)
            ->assertSee('Happy Hour:')
            ->assertSee('11:00 AM - 4:00 PM')
            ->assertDontSee('Swedish Massage')
            ->assertSee('Add Service');
    }

    public function test_admin_can_create_update_and_delete_a_service(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('services.store'), [
            'service_name' => 'New Massage',
            'duration' => 60,
            'happy_hour_price' => 300,
            'regular_price' => 350,
        ])->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Service added successfully.');

        $service = Service::query()->where('service_name', 'New Massage')->firstOrFail();
        $this->assertModelExists($service);

        $this->actingAs($admin)->put(route('services.update', $service), [
            'service_name' => 'Updated Massage',
            'duration' => 90,
            'happy_hour_price' => null,
            'regular_price' => 425,
        ])->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Service updated successfully.');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'service_name' => 'Updated Massage',
            'duration' => 90,
            'happy_hour_price' => null,
            'regular_price' => 425,
        ]);

        $this->actingAs($admin)->delete(route('services.destroy', $service))
            ->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Service deleted successfully.');

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_service_requires_at_least_one_price(): void
    {
        $response = $this->actingAs($this->admin())->from(route('services'))->post(route('services.store'), [
            'service_name' => 'Unpriced Massage',
            'duration' => 60,
            'happy_hour_price' => null,
            'regular_price' => null,
        ]);

        $response->assertRedirect(route('services'))
            ->assertSessionHasErrors(['happy_hour_price', 'regular_price']);

        $this->assertDatabaseMissing('services', ['service_name' => 'Unpriced Massage']);
    }

    public function test_service_cannot_be_deleted_when_used_by_an_appointment(): void
    {
        $admin = $this->admin();
        $service = Service::create([
            'service_name' => 'Booked Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $customer = Customer::create(['customer_name' => 'Test Customer']);
        $therapist = Therapist::create(['therapist_name' => 'Test Therapist']);

        Appointment::create([
            'customer_id' => $customer->id,
            'therapist_id' => $therapist->id,
            'service_id' => $service->id,
            'user_id' => $admin->id,
            'appointment_date' => '2026-12-01',
            'appointment_time' => '10:00:00',
            'reserved_price' => 350,
        ]);

        $this->actingAs($admin)->delete(route('services.destroy', $service))
            ->assertRedirect(route('services'))
            ->assertSessionHas('error', 'This service is already used in records and cannot be deleted.');

        $this->assertModelExists($service);
    }

    public function test_service_cannot_be_deleted_when_used_by_a_transaction_detail(): void
    {
        $admin = $this->admin();
        $service = Service::create([
            'service_name' => 'Sold Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $customer = Customer::create(['customer_name' => 'Test Customer']);
        $therapist = Therapist::create(['therapist_name' => 'Test Therapist']);
        $transaction = Transaction::create([
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'transaction_date' => '2026-11-27 10:00:00',
            'total_amount' => 350,
            'amount_paid' => 350,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'service_price' => 350,
            'quantity' => 1,
            'subtotal' => 350,
        ]);

        $this->actingAs($admin)->delete(route('services.destroy', $service))
            ->assertRedirect(route('services'))
            ->assertSessionHas('error', 'This service is already used in records and cannot be deleted.');

        $this->assertModelExists($service);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
