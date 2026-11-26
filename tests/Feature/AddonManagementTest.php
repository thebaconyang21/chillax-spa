<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Customer;
use App\Models\DetailAddon;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddonManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_an_addon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('addons.store'), [
            'addon_name' => 'Hot Stone',
            'price' => 150,
        ])->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Add-on added successfully.');

        $addon = Addon::query()->where('addon_name', 'Hot Stone')->firstOrFail();
        $this->assertModelExists($addon);

        $this->actingAs($admin)->put(route('addons.update', $addon), [
            'addon_name' => 'Warm Stone',
            'price' => 275.5,
        ])->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Add-on updated successfully.');

        $this->assertDatabaseHas('addons', [
            'id' => $addon->id,
            'addon_name' => 'Warm Stone',
            'price' => 275.5,
        ]);

        $this->actingAs($admin)->delete(route('addons.destroy', $addon))
            ->assertRedirect(route('services'))
            ->assertSessionHas('success', 'Add-on deleted successfully.');

        $this->assertDatabaseMissing('addons', ['id' => $addon->id]);
    }

    public function test_addon_requires_a_name_and_nonnegative_price(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->from(route('services'))
            ->post(route('addons.store'), [
                'addon_name' => '',
                'price' => -1,
            ]);

        $response->assertRedirect(route('services'))
            ->assertSessionHasErrors(['addon_name', 'price']);

        $this->assertDatabaseCount('addons', 0);
    }

    public function test_addon_cannot_be_deleted_when_used_by_a_detail_addon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $addon = Addon::create(['addon_name' => 'Referenced Add-on', 'price' => 150]);
        $service = Service::create([
            'service_name' => 'Massage',
            'duration' => 60,
            'regular_price' => 350,
        ]);
        $customer = Customer::create(['customer_name' => 'Test Customer']);
        $therapist = Therapist::create(['therapist_name' => 'Test Therapist']);
        $transaction = Transaction::create([
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'transaction_date' => '2026-11-27 10:00:00',
            'total_amount' => 500,
            'amount_paid' => 500,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);
        $transactionDetail = TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'service_price' => 350,
            'quantity' => 1,
            'subtotal' => 350,
        ]);

        DetailAddon::create([
            'transaction_detail_id' => $transactionDetail->id,
            'addon_id' => $addon->id,
            'addon_price' => 150,
        ]);

        $this->actingAs($admin)->delete(route('addons.destroy', $addon))
            ->assertRedirect(route('services'))
            ->assertSessionHas('error', 'This add-on is already used in records and cannot be deleted.');

        $this->assertModelExists($addon);
    }
}
