@extends('layouts.app')

@section('title', 'User Management')
@section('breadcrumb', 'User Management')

@section('content')
    @php
        $users = [
            ['id' => 1, 'name' => 'Jane Dela Cruz', 'username' => 'jane.admin', 'role' => 'Administrator', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Paolo Santos', 'username' => 'paolo.cashier', 'role' => 'Cashier-Staff', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Karla Mendoza', 'username' => 'karla.staff', 'role' => 'Cashier-Staff', 'status' => 'Inactive'],
            ['id' => 4, 'name' => 'Miguel Torres', 'username' => 'miguel.cashier', 'role' => 'Cashier-Staff', 'status' => 'Active'],
        ];
    @endphp

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

    <section class="mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:flex-row sm:items-end" aria-label="User filters">
        <div class="w-full sm:max-w-md">
            <label for="user-search" class="mb-1.5 block text-xs font-semibold text-ink/70">Search</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input id="user-search" type="search" placeholder="Search name or username..." class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 pl-9 pr-3 text-sm outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
            </div>
        </div>
        <div class="w-full sm:w-52">
            <label for="role-filter" class="mb-1.5 block text-xs font-semibold text-ink/70">Role</label>
            <select id="role-filter" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                <option>All</option>
                <option>Administrator</option>
                <option>Cashier-Staff</option>
            </select>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="users-heading">
        <div class="border-b border-maroon/10 px-5 py-4">
            <h2 id="users-heading" class="text-base font-semibold text-ink">Users</h2>
            <p id="user-count" class="mt-1 text-xs text-ink/55">{{ count($users) }} users</p>
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
                <tbody id="users-table-body" class="divide-y divide-maroon/5">
                    @foreach ($users as $user)
                        <tr data-user-row data-user-id="{{ $user['id'] }}" data-name="{{ $user['name'] }}" data-username="{{ $user['username'] }}" data-role="{{ $user['role'] }}" data-status="{{ $user['status'] }}" class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ $user['name'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/70">{{ $user['username'] }}</td>
                            <td class="px-5 py-4">
                                @if ($user['role'] === 'Administrator')
                                    <span class="inline-flex rounded-full bg-maroon/10 px-2.5 py-1 text-xs font-semibold text-maroon">Administrator</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gold/20 px-2.5 py-1 text-xs font-semibold text-maroon">Cashier-Staff</span>
                                @endif
                            </td>
                            <td class="px-5 py-4" data-status-cell>
                                @if ($user['status'] === 'Active')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-user-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    <button type="button" data-user-action="toggle" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">
                                        {{ $user['status'] === 'Active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button type="button" data-user-action="delete" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="no-users" class="hidden">
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-ink/55">No users match your filters.</td>
                    </tr>
                </tbody>
            </table>
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
            <form id="user-form" class="space-y-4 p-5 sm:p-6">
                <input id="user-id" type="hidden">
                <div>
                    <label for="full-name" class="mb-1.5 block text-sm font-medium text-ink">Full Name</label>
                    <input id="full-name" type="text" required autocomplete="name" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-ink">Username</label>
                    <input id="username" type="text" required autocomplete="username" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-ink">Password</label>
                        <input id="password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    </div>
                    <div>
                        <label for="confirm-password" class="mb-1.5 block text-sm font-medium text-ink">Confirm Password</label>
                        <input id="confirm-password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    </div>
                </div>
                <p id="password-error" class="hidden rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700">Passwords do not match.</p>
                <div>
                    <label for="user-role" class="mb-1.5 block text-sm font-medium text-ink">Role</label>
                    <select id="user-role" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        <option value="Administrator">Administrator</option>
                        <option value="Cashier-Staff">Cashier-Staff</option>
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
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancel-delete" type="button" class="min-h-10 rounded-lg border border-maroon/15 px-4 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                <button id="confirm-delete" type="button" class="min-h-10 rounded-lg bg-red-700 px-4 text-sm font-semibold text-white transition hover:bg-red-800">Delete</button>
            </div>
        </section>
    </div>

    <script>
        const userModal = document.getElementById('user-modal');
        const deleteModal = document.getElementById('delete-modal');
        const userForm = document.getElementById('user-form');
        const searchInput = document.getElementById('user-search');
        const roleFilter = document.getElementById('role-filter');
        let userIdPendingDelete = null;

        function setModal(modal, isOpen) {
            modal.classList.toggle('hidden', !isOpen);
            modal.classList.toggle('flex', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));
        }

        function openUserModal(row = null) {
            const isEditing = Boolean(row);
            document.getElementById('user-modal-title').textContent = isEditing ? 'Edit User' : 'Add User';
            document.getElementById('user-modal-description').textContent = isEditing ? 'Update this staff account.' : 'Create a new spa staff account.';
            document.getElementById('user-id').value = isEditing ? row.dataset.userId : '';
            document.getElementById('full-name').value = isEditing ? row.dataset.name : '';
            document.getElementById('username').value = isEditing ? row.dataset.username : '';
            document.getElementById('password').value = '';
            document.getElementById('confirm-password').value = '';
            document.getElementById('password-error').classList.add('hidden');
            document.getElementById('password').required = !isEditing;
            document.getElementById('confirm-password').required = !isEditing;
            document.getElementById('user-role').value = isEditing ? row.dataset.role : 'Cashier-Staff';
            setModal(userModal, true);
            document.getElementById('full-name').focus();
        }

        function closeUserModal() {
            setModal(userModal, false);
        }

        function filterUsers() {
            const query = searchInput.value.trim().toLowerCase();
            const role = roleFilter.value;
            let visibleCount = 0;
            document.querySelectorAll('[data-user-row]').forEach((row) => {
                const matchesSearch = !query || row.dataset.name.toLowerCase().includes(query) || row.dataset.username.toLowerCase().includes(query);
                const matchesRole = role === 'All' || row.dataset.role === role;
                const isVisible = matchesSearch && matchesRole;
                row.classList.toggle('hidden', !isVisible);
                if (isVisible) visibleCount += 1;
            });
            document.getElementById('no-users').classList.toggle('hidden', visibleCount !== 0);
            document.getElementById('user-count').textContent = `${visibleCount} user${visibleCount === 1 ? '' : 's'}`;
        }

        document.getElementById('add-user-button').addEventListener('click', () => openUserModal());
        document.getElementById('close-user-modal').addEventListener('click', closeUserModal);
        document.getElementById('cancel-user-modal').addEventListener('click', closeUserModal);
        searchInput.addEventListener('input', filterUsers);
        roleFilter.addEventListener('change', filterUsers);

        userModal.addEventListener('click', (event) => {
            if (event.target === userModal) closeUserModal();
        });

        document.querySelectorAll('[data-user-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const row = button.closest('[data-user-row]');
                const action = button.dataset.userAction;

                if (action === 'edit') {
                    openUserModal(row);
                } else if (action === 'toggle') {
                    const isActive = row.dataset.status === 'Active';
                    row.dataset.status = isActive ? 'Inactive' : 'Active';
                    const statusCell = row.querySelector('[data-status-cell]');
                    statusCell.innerHTML = isActive
                        ? '<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Inactive</span>'
                        : '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>';
                    button.textContent = isActive ? 'Activate' : 'Deactivate';
                } else if (action === 'delete') {
                    userIdPendingDelete = row.dataset.userId;
                    document.getElementById('delete-user-name').textContent = row.dataset.name;
                    setModal(deleteModal, true);
                }
            });
        });

        userForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            if (password !== confirmPassword) {
                document.getElementById('password-error').classList.remove('hidden');
                return;
            }
            document.getElementById('password-error').classList.add('hidden');

            const id = document.getElementById('user-id').value;
            const name = document.getElementById('full-name').value.trim();
            const username = document.getElementById('username').value.trim();
            const role = document.getElementById('user-role').value;

            if (id) {
                const row = document.querySelector(`[data-user-row][data-user-id="${id}"]`);
                row.dataset.name = name;
                row.dataset.username = username;
                row.dataset.role = role;
                row.children[0].textContent = name;
                row.children[1].textContent = username;
                row.children[2].innerHTML = role === 'Administrator'
                    ? '<span class="inline-flex rounded-full bg-maroon/10 px-2.5 py-1 text-xs font-semibold text-maroon">Administrator</span>'
                    : '<span class="inline-flex rounded-full bg-gold/20 px-2.5 py-1 text-xs font-semibold text-maroon">Cashier-Staff</span>';
            } else {
                const tbody = document.getElementById('users-table-body');
                const newId = String(Date.now());
                const row = document.createElement('tr');
                row.dataset.userRow = '';
                row.dataset.userId = newId;
                row.dataset.name = name;
                row.dataset.username = username;
                row.dataset.role = role;
                row.dataset.status = 'Active';
                row.className = 'transition hover:bg-cream/45';
                row.innerHTML = `
                    <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">${name}</td>
                    <td class="whitespace-nowrap px-5 py-4 text-ink/70">${username}</td>
                    <td class="px-5 py-4">
                        ${role === 'Administrator'
                            ? '<span class="inline-flex rounded-full bg-maroon/10 px-2.5 py-1 text-xs font-semibold text-maroon">Administrator</span>'
                            : '<span class="inline-flex rounded-full bg-gold/20 px-2.5 py-1 text-xs font-semibold text-maroon">Cashier-Staff</span>'}
                    </td>
                    <td class="px-5 py-4" data-status-cell><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span></td>
                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-2">
                            <button type="button" data-user-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                            <button type="button" data-user-action="toggle" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Deactivate</button>
                            <button type="button" data-user-action="delete" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                        </div>
                    </td>`;
                tbody.insertBefore(row, document.getElementById('no-users'));
                attachUserActionListeners(row);
            }
            closeUserModal();
            filterUsers();
        });

        function attachUserActionListeners(scope) {
            scope.querySelectorAll('[data-user-action]').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-user-row]');
                    const action = button.dataset.userAction;
                    if (action === 'edit') openUserModal(row);
                    if (action === 'toggle') {
                        const isActive = row.dataset.status === 'Active';
                        row.dataset.status = isActive ? 'Inactive' : 'Active';
                        row.querySelector('[data-status-cell]').innerHTML = isActive
                            ? '<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Inactive</span>'
                            : '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>';
                        button.textContent = isActive ? 'Activate' : 'Deactivate';
                    }
                    if (action === 'delete') {
                        userIdPendingDelete = row.dataset.userId;
                        document.getElementById('delete-user-name').textContent = row.dataset.name;
                        setModal(deleteModal, true);
                    }
                });
            });
        }

        document.getElementById('cancel-delete').addEventListener('click', () => setModal(deleteModal, false));
        document.getElementById('confirm-delete').addEventListener('click', () => {
            const row = document.querySelector(`[data-user-row][data-user-id="${userIdPendingDelete}"]`);
            if (row) row.remove();
            setModal(deleteModal, false);
            filterUsers();
        });
        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) setModal(deleteModal, false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeUserModal();
                setModal(deleteModal, false);
            }
        });
    </script>
@endsection
