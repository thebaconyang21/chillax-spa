<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_route_renders_database_accounts_with_search_role_filter_and_pagination(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Current Administrator']);

        for ($index = 1; $index <= 12; $index++) {
            User::factory()->create([
                'name' => 'Search Staff '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'username' => 'search_staff_'.$index,
                'role' => 'cashier',
                'is_active' => $index !== 2,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('users', [
            'search' => 'Search Staff',
            'role' => 'cashier',
        ]));

        $response->assertOk()
            ->assertViewIs('users')
            ->assertSee('User Management')
            ->assertSee('Search Staff 01')
            ->assertSee('Search Staff 10')
            ->assertDontSee('Search Staff 11')
            ->assertSee('value="Search Staff"', false)
            ->assertSee('value="cashier"', false)
            ->assertSee('search=Search%20Staff', false)
            ->assertSee('page=2', false)
            ->assertSee('Active')
            ->assertSee('Inactive')
            ->assertDontSee('Jane Dela Cruz');
    }

    public function test_admin_can_create_update_and_delete_a_user_and_password_is_hashed_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'username' => 'root_admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Staff Member',
            'username' => 'new_staff',
            'password' => 'initial-password',
            'password_confirmation' => 'initial-password',
            'role' => 'cashier',
        ])->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User created successfully.');

        $user = User::query()->where('username', 'new_staff')->firstOrFail();
        $this->assertTrue($user->is_active);
        $this->assertNull($user->email);
        $this->assertTrue(Hash::check('initial-password', $user->getRawOriginal('password')));
        $initialPasswordHash = $user->getRawOriginal('password');

        $this->actingAs($admin)->put(route('users.update', $user), [
            'user_id' => $user->id,
            'name' => 'Updated Staff Member',
            'username' => 'new_staff',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'cashier',
        ])->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User updated successfully.');

        $user->refresh();
        $this->assertSame('Updated Staff Member', $user->name);
        $this->assertSame($initialPasswordHash, $user->getRawOriginal('password'));

        $this->actingAs($admin)->put(route('users.update', $user), [
            'user_id' => $user->id,
            'name' => 'Updated Staff Member',
            'username' => 'new_staff',
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
            'role' => 'cashier',
        ])->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User updated successfully.');

        $user->refresh();
        $this->assertTrue(Hash::check('replacement-password', $user->getRawOriginal('password')));

        $this->actingAs($admin)->delete(route('users.destroy', $user))
            ->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User deleted successfully.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_store_validation_rejects_invalid_username_and_unconfirmed_password(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->from(route('users'))
            ->post(route('users.store'), [
                'name' => 'Invalid Staff',
                'username' => 'bad username',
                'password' => 'short',
                'password_confirmation' => 'different',
                'role' => 'cashier',
            ]);

        $response->assertRedirect(route('users'))
            ->assertSessionHasErrors(['username', 'password']);

        $this->assertDatabaseMissing('users', ['name' => 'Invalid Staff']);
    }

    public function test_admin_can_toggle_another_users_active_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);

        $this->actingAs($admin)->patch(route('users.toggle', $cashier))
            ->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User deactivated successfully.');

        $cashier->refresh();
        $this->assertFalse($cashier->is_active);

        $this->actingAs($admin)->patch(route('users.toggle', $cashier))
            ->assertRedirect(route('users'))
            ->assertSessionHas('success', 'User activated successfully.');

        $cashier->refresh();
        $this->assertTrue($cashier->is_active);
    }

    public function test_admin_cannot_change_role_deactivate_or_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'username' => 'root_account']);

        $this->actingAs($admin)->put(route('users.update', $admin), [
            'user_id' => $admin->id,
            'name' => $admin->name,
            'username' => $admin->username,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'cashier',
        ])->assertRedirect(route('users'))
            ->assertSessionHas('error', 'You cannot change your own role.');

        $this->actingAs($admin)->patch(route('users.toggle', $admin))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'You cannot deactivate your own account.');

        $this->actingAs($admin)->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'You cannot delete your own account.');

        $admin->refresh();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
    }

    public function test_last_active_admin_cannot_be_demoted_deactivated_or_deleted(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'username' => 'other_staff']);
        $lastAdmin = User::factory()->create(['role' => 'admin', 'username' => 'last_admin', 'is_active' => true]);
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($cashier);

        $this->put(route('users.update', $lastAdmin), [
            'user_id' => $lastAdmin->id,
            'name' => $lastAdmin->name,
            'username' => $lastAdmin->username,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'cashier',
        ])->assertRedirect(route('users'))
            ->assertSessionHas('error', 'The last active administrator cannot be demoted.');

        $this->patch(route('users.toggle', $lastAdmin))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'The last active administrator cannot be deactivated.');

        $this->delete(route('users.destroy', $lastAdmin))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'The last active administrator cannot be deleted.');

        $lastAdmin->refresh();
        $this->assertSame('admin', $lastAdmin->role);
        $this->assertTrue($lastAdmin->is_active);
    }

    public function test_user_with_appointments_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Appointment Customer']);
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
            'user_id' => $cashier->id,
            'appointment_date' => '2026-11-20',
            'appointment_time' => '10:00:00',
            'reserved_price' => 350,
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $cashier))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'This user has existing records. Deactivate the account instead.');

        $this->assertModelExists($cashier);
    }

    public function test_user_with_transactions_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $customer = Customer::create(['customer_name' => 'Transaction Customer']);
        Transaction::create([
            'customer_id' => $customer->id,
            'user_id' => $cashier->id,
            'transaction_date' => '2026-11-20 11:00:00',
            'total_amount' => 350,
            'amount_paid' => 350,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $cashier))
            ->assertRedirect(route('users'))
            ->assertSessionHas('error', 'This user has existing records. Deactivate the account instead.');

        $this->assertModelExists($cashier);
    }
}
