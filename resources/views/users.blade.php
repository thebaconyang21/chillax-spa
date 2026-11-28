@extends('layouts.app')

@section('title', 'User Management')
@section('breadcrumb', 'User Management')

@section('content')
    @if (session('success'))
        <div role="status" class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">User Management</h1>
            <p class="mt-1 text-sm text-ink/65">Manage staff accounts, roles, and access status.</p>
        </div>
        <button id="add-user-button" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-gold px-5 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add User
        </button>
    </div>

    <form method="GET" action="{{ route('users') }}" class="mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:flex-row sm:items-end" aria-label="User filters">
        <div class="w-full sm:max-w-md">
            <label for="user-search" class="mb-1.5 block text-xs font-semibold text-ink/70">Search</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input id="user-search" name="search" type="search" value="{{ $search }}" placeholder="Search name or username..." class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 pl-9 pr-3 text-sm outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
            </div>
        </div>
        <div class="w-full sm:w-52">
            <label for="role-filter" class="mb-1.5 block text-xs font-semibold text-ink/70">Role</label>
            <select id="role-filter" name="role" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                <option value="" @selected($role === '')>All roles</option>
                <option value="admin" @selected($role === 'admin')>Administrator</option>
                <option value="cashier" @selected($role === 'cashier')>Cashier-Staff</option>
            </select>
        </div>
        <button type="submit" class="h-11 rounded-lg bg-maroon px-5 text-sm font-semibold text-white transition hover:bg-maroon/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Apply Filters</button>
    </form>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="users-heading">
        <div class="border-b border-maroon/10 px-5 py-4">
            <h2 id="users-heading" class="text-base font-semibold text-ink">Users</h2>
            <p class="mt-1 text-xs text-ink/55">{{ $users->total() }} users</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Name</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Username</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Role</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-maroon/5">
                    @forelse ($users as $user)
                        @php
                            $isCurrentUser = auth()->id() === $user->id;
                        @endphp
                        <tr data-user-row data-user-id="{{ $user->id }}" data-name="{{ $user->name }}" data-username="{{ $user->username }}" data-role="{{ $user->role }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-current-user="{{ $isCurrentUser ? 'true' : 'false' }}" class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ $user->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/70">{{ $user->username }}</td>
                            <td class="px-5 py-4">
                                @if ($user->role === 'admin')
                                    <span class="inline-flex rounded-full bg-maroon/10 px-2.5 py-1 text-xs font-semibold text-maroon">Administrator</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gold/20 px-2.5 py-1 text-xs font-semibold text-maroon">Cashier-Staff</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-user-action="edit" data-confirm-edit data-record-name="{{ $user->name }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    @if (! $isCurrentUser)
                                        <form method="POST" action="{{ route('users.toggle', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <button type="button" data-user-action="delete" data-delete-url="{{ route('users.destroy', $user) }}" data-delete-name="{{ $user->name }}" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-ink/55">No users match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col justify-between gap-3 border-t border-maroon/10 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
            <p class="text-xs text-ink/60">
                Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
            </p>
            @if ($users->hasPages())
                <nav class="flex flex-wrap items-center justify-end gap-1" aria-label="User pagination">
                    @if ($users->onFirstPage())
                        <span class="rounded-lg border border-maroon/10 px-3 py-2 text-xs text-ink/35" aria-disabled="true">Previous</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Previous</a>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <a href="{{ $url }}" @if ($page === $users->currentPage()) aria-current="page" @endif class="min-w-9 rounded-lg border px-3 py-2 text-center text-xs font-medium transition {{ $page === $users->currentPage() ? 'border-maroon bg-maroon text-white' : 'border-maroon/15 text-maroon hover:border-gold hover:bg-gold/10' }}">{{ $page }}</a>
                    @endforeach

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Next</a>
                    @else
                        <span class="rounded-lg border border-maroon/10 px-3 py-2 text-xs text-ink/35" aria-disabled="true">Next</span>
                    @endif
                </nav>
            @endif
        </div>
    </section>

    <div id="user-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-lg rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="user-modal-title" class="text-lg font-semibold text-maroon">Add User</h2>
                    <p id="user-modal-description" class="mt-1 text-xs text-ink/55">Create a new spa staff account.</p>
                </div>
                <button id="close-user-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="user-form" method="POST" action="{{ route('users.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                @method('PUT')
                <input id="user-id" name="user_id" type="hidden" value="{{ old('user_id') }}">
                <div>
                    <label for="full-name" class="mb-1.5 block text-sm font-medium text-ink">Full Name</label>
                    <input id="full-name" name="name" type="text" value="{{ old('name') }}" required maxlength="100" autocomplete="name" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-ink">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required minlength="4" maxlength="30" autocomplete="username" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-ink">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    </div>
                    <div>
                        <label for="confirm-password" class="mb-1.5 block text-sm font-medium text-ink">Confirm Password</label>
                        <input id="confirm-password" name="password_confirmation" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    </div>
                </div>
                <p id="password-hint" class="hidden text-xs text-ink/55">Leave blank to keep the current password.</p>
                <div>
                    <label for="user-role" class="mb-1.5 block text-sm font-medium text-ink">Role</label>
                    <select id="user-role" name="role" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        <option value="admin">Administrator</option>
                        <option value="cashier">Cashier-Staff</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 border-t border-maroon/10 pt-4">
                    <button id="cancel-user-modal" type="button" class="min-h-10 rounded-lg border border-maroon/15 px-4 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-10 rounded-lg bg-maroon px-5 text-sm font-semibold text-white transition hover:bg-maroon/90">Save User</button>
                </div>
            </form>
        </section>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/60 p-4" aria-hidden="true">
        <section class="w-full max-w-md rounded-2xl border border-red-100 bg-white p-5 shadow-2xl sm:p-6" role="alertdialog" aria-modal="true" aria-labelledby="delete-modal-title">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 text-red-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
            </div>
            <h2 id="delete-modal-title" class="mt-4 text-lg font-semibold text-ink">Delete User</h2>
            <p class="mt-2 text-sm leading-relaxed text-ink/65">Are you sure you want to delete <strong id="delete-user-name" class="text-ink"></strong>? This action cannot be undone.</p>
            <form id="delete-form" method="POST" action="{{ route('users') }}" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button id="cancel-delete" type="button" class="min-h-10 rounded-lg border border-maroon/15 px-4 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                <button type="submit" class="min-h-10 rounded-lg bg-red-700 px-4 text-sm font-semibold text-white transition hover:bg-red-800">Delete</button>
            </form>
        </section>
    </div>

    <script>
        const userModal = document.getElementById('user-modal');
        const deleteModal = document.getElementById('delete-modal');
        const userForm = document.getElementById('user-form');
        const updateUrlTemplate = @js(route('users.update', ['user' => '__USER__']));
        const deleteForm = document.getElementById('delete-form');
        const currentUserId = @js(auth()->id());

        function setModal(modal, isOpen) {
            modal.classList.toggle('hidden', !isOpen);
            modal.classList.toggle('flex', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));
        }

        function setMethodOverride(form, method) {
            let methodInput = form.querySelector('input[name="_method"]');

            if (method) {
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }

                methodInput.value = method;
            } else if (methodInput) {
                methodInput.remove();
            }
        }

        function findUserRow(id) {
            return [...document.querySelectorAll('[data-user-row]')]
                .find((row) => row.dataset.userId === String(id));
        }

        function setSelfRoleValue(isCurrentUser, role) {
            const roleSelect = document.getElementById('user-role');
            let hiddenRole = userForm.querySelector('input[data-self-role]');
            roleSelect.disabled = isCurrentUser;

            if (isCurrentUser) {
                if (!hiddenRole) {
                    hiddenRole = document.createElement('input');
                    hiddenRole.type = 'hidden';
                    hiddenRole.name = 'role';
                    hiddenRole.dataset.selfRole = '';
                    userForm.appendChild(hiddenRole);
                }

                hiddenRole.value = role;
            } else if (hiddenRole) {
                hiddenRole.remove();
            }
        }

        function openUserModal(user = null) {
            userForm.reset();
            const isEditing = Boolean(user);
            const isCurrentUser = isEditing && String(user.id) === String(currentUserId);
            document.getElementById('user-modal-title').textContent = isEditing ? 'Edit User' : 'Add User';
            document.getElementById('user-modal-description').textContent = isEditing ? 'Update this staff account.' : 'Create a new spa staff account.';
            document.getElementById('user-id').value = user?.id ?? '';
            document.getElementById('full-name').value = user?.name ?? '';
            document.getElementById('username').value = user?.username ?? '';
            document.getElementById('password').value = '';
            document.getElementById('confirm-password').value = '';
            document.getElementById('password').required = !isEditing;
            document.getElementById('confirm-password').required = !isEditing;
            document.getElementById('password-hint').classList.toggle('hidden', !isEditing);
            document.getElementById('user-role').value = user?.role ?? 'cashier';
            setSelfRoleValue(isCurrentUser, user?.role ?? 'cashier');
            userForm.action = isEditing
                ? updateUrlTemplate.replace('__USER__', encodeURIComponent(user.id))
                : @js(route('users.store'));
            setMethodOverride(userForm, isEditing ? 'PUT' : null);
            setModal(userModal, true);
            document.getElementById('full-name').focus();
        }

        document.getElementById('password').addEventListener('input', (event) => {
            document.getElementById('confirm-password').required =
                !document.getElementById('user-id').value || event.target.value.length > 0;
        });

        document.getElementById('add-user-button').addEventListener('click', () => openUserModal());
        document.getElementById('close-user-modal').addEventListener('click', () => setModal(userModal, false));
        document.getElementById('cancel-user-modal').addEventListener('click', () => setModal(userModal, false));
        userModal.addEventListener('click', (event) => {
            if (event.target === userModal) {
                setModal(userModal, false);
            }
        });

        document.querySelectorAll('[data-user-action="edit"]').forEach((button) => {
            button.addEventListener('click', () => {
                const row = button.closest('[data-user-row]');
                openUserModal({
                    id: row.dataset.userId,
                    name: row.dataset.name,
                    username: row.dataset.username,
                    role: row.dataset.role,
                });
            });
        });

        document.querySelectorAll('[data-user-action="delete"]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('delete-user-name').textContent = button.dataset.deleteName;
                deleteForm.action = button.dataset.deleteUrl;
                setModal(deleteModal, true);
            });
        });

        document.getElementById('cancel-delete').addEventListener('click', () => setModal(deleteModal, false));
        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                setModal(deleteModal, false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setModal(userModal, false);
                setModal(deleteModal, false);
            }
        });

        @if ($errors->any())
            const failedUserId = @js(old('user_id'));
            const failedUserRow = findUserRow(failedUserId);
            const failedUser = failedUserRow
                ? {
                    id: failedUserRow.dataset.userId,
                    name: failedUserRow.dataset.name,
                    username: failedUserRow.dataset.username,
                    role: failedUserRow.dataset.role,
                }
                : (failedUserId
                    ? {
                        id: failedUserId,
                        name: @js(old('name', '')),
                        username: @js(old('username', '')),
                        role: @js(old('role', 'cashier')),
                    }
                    : null);

            openUserModal(failedUser);
            document.getElementById('full-name').value = @js(old('name', ''));
            document.getElementById('username').value = @js(old('username', ''));
            document.getElementById('user-role').value = @js(old('role', 'cashier'));
        @endif
    </script>
@endsection
