<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $search = is_string($search) ? trim($search) : '';
        $role = $request->query('role');
        $role = in_array($role, ['admin', 'cashier'], true) ? $role : '';

        $users = User::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            })
            ->when($role !== '', fn (Builder $query): Builder => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users', compact('users', 'search', 'role'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'alpha_dash', 'min:4', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'cashier'])],
        ]);

        User::create(Arr::only($validated, ['name', 'username', 'password', 'role']));

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'alpha_dash',
                'min:4',
                'max:30',
                Rule::unique('users', 'username')->ignore($user),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'cashier'])],
        ]);

        $attributes = Arr::only($validated, ['name', 'username', 'role']);

        if ($request->filled('password')) {
            $attributes['password'] = $validated['password'];
        }

        $result = DB::transaction(function () use ($user, $attributes): array {
            $activeAdmins = $this->lockActiveAdmins();
            $currentUser = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if ((int) Auth::id() === (int) $currentUser->getKey() && $currentUser->role !== $attributes['role']) {
                return ['type' => 'error', 'message' => 'You cannot change your own role.'];
            }

            if ($currentUser->is_active && $currentUser->role === 'admin' && $attributes['role'] !== 'admin' && $activeAdmins->count() <= 1) {
                return ['type' => 'error', 'message' => 'The last active administrator cannot be demoted.'];
            }

            $currentUser->update($attributes);

            return ['type' => 'success', 'message' => 'User updated successfully.'];
        });

        return redirect()->route('users')->with($result['type'], $result['message']);
    }

    public function toggle(User $user): RedirectResponse
    {
        $result = DB::transaction(function () use ($user): array {
            $activeAdmins = $this->lockActiveAdmins();
            $currentUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $willBeActive = ! $currentUser->is_active;

            if (! $willBeActive && (int) Auth::id() === (int) $currentUser->getKey()) {
                return ['type' => 'error', 'message' => 'You cannot deactivate your own account.'];
            }

            if (! $willBeActive && $currentUser->role === 'admin' && $activeAdmins->count() <= 1) {
                return ['type' => 'error', 'message' => 'The last active administrator cannot be deactivated.'];
            }

            $currentUser->update(['is_active' => $willBeActive]);

            return [
                'type' => 'success',
                'message' => $willBeActive ? 'User activated successfully.' : 'User deactivated successfully.',
            ];
        });

        return redirect()->route('users')->with($result['type'], $result['message']);
    }

    public function destroy(User $user): RedirectResponse
    {
        $result = DB::transaction(function () use ($user): array {
            $activeAdmins = $this->lockActiveAdmins();
            $currentUser = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if ((int) Auth::id() === (int) $currentUser->getKey()) {
                return ['type' => 'error', 'message' => 'You cannot delete your own account.'];
            }

            if ($currentUser->is_active && $currentUser->role === 'admin' && $activeAdmins->count() <= 1) {
                return ['type' => 'error', 'message' => 'The last active administrator cannot be deleted.'];
            }

            if ($currentUser->appointments()->exists() || $currentUser->transactions()->exists()) {
                return ['type' => 'error', 'message' => 'This user has existing records. Deactivate the account instead.'];
            }

            $currentUser->delete();

            return ['type' => 'success', 'message' => 'User deleted successfully.'];
        });

        return redirect()->route('users')->with($result['type'], $result['message']);
    }

    private function lockActiveAdmins(): EloquentCollection
    {
        return User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id']);
    }
}
