@extends('layouts.app')

@section('title', 'Customer Management')
@section('breadcrumb', 'Customer Management')

@section('content')
    @php
        $customers = [
            [
                'id' => 1,
                'name' => 'Maria Santos',
                'phone' => '0917 123 4567',
                'visits' => 8,
                'last_visit' => 'Nov 20, 2026',
                'history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Swedish Massage (60 mins)', 'therapist' => 'Therapist B', 'status' => 'Completed'],
                    ['date' => 'Nov 12, 2026', 'service' => 'Foot Massage', 'therapist' => 'Therapist A', 'status' => 'Completed'],
                    ['date' => 'Oct 28, 2026', 'service' => 'Combo (Swedish + Add-on)', 'therapist' => 'Therapist C', 'status' => 'Completed'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Ana Reyes',
                'phone' => '0917 555 0142',
                'visits' => 3,
                'last_visit' => 'Nov 18, 2026',
                'history' => [
                    ['date' => 'Nov 18, 2026', 'service' => 'Combo (Swedish + Add-on)', 'therapist' => 'Therapist A', 'status' => 'Completed'],
                    ['date' => 'Oct 30, 2026', 'service' => 'Swedish Massage (60 mins)', 'therapist' => 'Therapist B', 'status' => 'Completed'],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Liza Cruz',
                'phone' => '0920 444 7621',
                'visits' => 1,
                'last_visit' => 'Nov 10, 2026',
                'history' => [
                    ['date' => 'Nov 10, 2026', 'service' => 'Foot Massage', 'therapist' => 'Therapist A', 'status' => 'Completed'],
                ],
            ],
            [
                'id' => 4,
                'name' => 'John Dela Cruz',
                'phone' => '0916 333 9081',
                'visits' => 0,
                'last_visit' => '—',
                'history' => [],
            ],
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Customer Management</h1>
            <p class="mt-1 text-sm text-ink/65">Manage customer profiles and view their visit history.</p>
        </div>
        <button id="add-customer" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-gold px-4 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add Customer
        </button>
    </div>

    <div class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.8fr)]">
        <section class="min-w-0 overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="customers-heading">
            <div class="flex flex-col gap-3 border-b border-maroon/10 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 id="customers-heading" class="text-base font-semibold text-ink">Customers</h2>
                    <p class="mt-1 text-xs text-ink/55">Browse customer profiles and visits</p>
                </div>
                <label class="relative block w-full sm:max-w-xs">
                    <span class="sr-only">Search customers</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="customer-search" type="search" placeholder="Search customer name..." class="h-11 w-full rounded-full border border-maroon/15 bg-white pl-10 pr-4 text-sm outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/30">
                </label>
            </div>

            <div class="min-w-0 overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="bg-cream/75 text-xs text-ink/70">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 font-semibold sm:px-5">Customer Name</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold sm:px-5">Contact Number</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold sm:px-5">Total Visits</th>
                            <th scope="col" class="px-4 py-3.5 font-semibold sm:px-5">Last Visit</th>
                            <th scope="col" class="px-4 py-3.5 text-right font-semibold sm:px-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customer-table-body" class="divide-y divide-maroon/5">
                        @foreach ($customers as $customer)
                            @php
                                $isReturningCustomer = $customer['visits'] > 0;
                                $badgeStyle = $isReturningCustomer ? 'bg-emerald-50 text-emerald-700' : 'bg-gold/15 text-maroon';
                            @endphp
                            <tr
                                data-customer-row
                                data-customer-id="{{ $customer['id'] }}"
                                data-customer-name="{{ $customer['name'] }}"
                                data-phone="{{ $customer['phone'] }}"
                                data-visits="{{ $customer['visits'] }}"
                                data-last-visit="{{ $customer['last_visit'] }}"
                                data-history="{{ json_encode($customer['history']) }}"
                                class="transition hover:bg-cream/45 {{ $customer['id'] === 1 ? 'bg-gold/10' : '' }}">
                                <td class="px-4 py-4 sm:px-5">
                                    <div class="font-medium text-ink">{{ $customer['name'] }}</div>
                                    <span data-customer-badge class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badgeStyle }}">
                                        {{ $isReturningCustomer ? 'Returning Customer' : 'New Customer' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $customer['phone'] }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $customer['visits'] }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $customer['last_visit'] }}</td>
                                <td class="px-4 py-4 sm:px-5">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-customer-action="view" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">View</button>
                                        <button type="button" data-customer-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="empty-customers" class="hidden">
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-ink/55">No customers match your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="min-w-0 space-y-5" aria-label="Selected customer">
            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="customer-details-heading">
                <div class="border-b border-maroon/10 px-5 py-4">
                    <h2 id="customer-details-heading" class="flex items-center gap-2 text-base font-semibold text-ink">
                        <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                        Customer Details
                    </h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4 border-b border-maroon/10 pb-4">
                        <div id="customer-avatar" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gold/20 text-lg font-semibold text-maroon" aria-hidden="true">MS</div>
                        <div class="min-w-0">
                            <h3 id="detail-customer-name" class="truncate text-lg font-semibold text-ink">Maria Santos</h3>
                            <p id="detail-phone" class="mt-1 text-sm text-ink/65">0917 123 4567</p>
                            <span id="detail-customer-badge" class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Returning Customer</span>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 py-4">
                        <div class="rounded-lg bg-cream/70 p-3">
                            <dt class="text-xs text-ink/55">Total Visits</dt>
                            <dd id="detail-total-visits" class="mt-1 text-lg font-semibold text-maroon">8</dd>
                        </div>
                        <div class="rounded-lg bg-cream/70 p-3">
                            <dt class="text-xs text-ink/55">Last Visit</dt>
                            <dd id="detail-last-visit" class="mt-1 text-sm font-semibold text-ink">Nov 20, 2026</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="customer-history-heading">
                <div class="border-b border-maroon/10 px-5 py-4">
                    <h2 id="customer-history-heading" class="font-display text-3xl text-maroon">Appointment History</h2>
                    <p class="mt-1 text-xs text-ink/55">Previous visits and services</p>
                </div>
                <div id="customer-history" class="divide-y divide-maroon/5">
                    <article class="flex items-start gap-3 p-4">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-gold" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-ink">Swedish Massage (60 mins)</h3>
                                <span class="text-xs text-ink/55">Nov 20, 2026</span>
                            </div>
                            <p class="mt-1 text-xs text-ink/60">Therapist B <span aria-hidden="true">&middot;</span> Completed</p>
                        </div>
                    </article>
                    <article class="flex items-start gap-3 p-4">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-gold" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-ink">Foot Massage</h3>
                                <span class="text-xs text-ink/55">Nov 12, 2026</span>
                            </div>
                            <p class="mt-1 text-xs text-ink/60">Therapist A <span aria-hidden="true">&middot;</span> Completed</p>
                        </div>
                    </article>
                    <article class="flex items-start gap-3 p-4">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-gold" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-ink">Combo (Swedish + Add-on)</h3>
                                <span class="text-xs text-ink/55">Oct 28, 2026</span>
                            </div>
                            <p class="mt-1 text-xs text-ink/60">Therapist C <span aria-hidden="true">&middot;</span> Completed</p>
                        </div>
                    </article>
                </div>
            </section>
        </aside>
    </div>

    <div id="customer-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-lg rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="customer-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="customer-modal-title" class="text-lg font-semibold text-maroon">Add Customer</h2>
                    <p class="mt-1 text-xs text-ink/55">Enter the customer&apos;s contact details.</p>
                </div>
                <button id="close-customer-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="customer-form" class="space-y-4 p-5 sm:p-6">
                <input id="customer-id" type="hidden">
                <div>
                    <label for="customer-name" class="mb-1.5 block text-sm font-medium text-ink">Customer Name</label>
                    <input id="customer-name" name="name" type="text" required autocomplete="name" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="customer-phone" class="mb-1.5 block text-sm font-medium text-ink">Contact Number</label>
                    <input id="customer-phone" name="phone" type="tel" required autocomplete="tel" placeholder="e.g. 0917 123 4567" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button id="cancel-customer" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Customer</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const customerModal = document.getElementById('customer-modal');
        const customerForm = document.getElementById('customer-form');
        const customerTableBody = document.getElementById('customer-table-body');
        const customerSearch = document.getElementById('customer-search');
        let nextCustomerId = Math.max(...[...document.querySelectorAll('[data-customer-row]')].map((row) => Number(row.dataset.customerId))) + 1;
        let selectedCustomerRow = document.querySelector('[data-customer-id="1"]');
        let modalTrigger = null;

        function setCustomerModalOpen(isOpen) {
            customerModal.classList.toggle('hidden', !isOpen);
            customerModal.classList.toggle('flex', isOpen);
            customerModal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                document.getElementById('customer-name').focus();
            } else if (modalTrigger) {
                modalTrigger.focus();
            }
        }

        function customerBadgeText(visits) {
            return Number(visits) > 0 ? 'Returning Customer' : 'New Customer';
        }

        function customerBadgeStyle(visits) {
            return Number(visits) > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gold/15 text-maroon';
        }

        function createCustomerRow(customer) {
            const row = document.createElement('tr');
            row.dataset.customerRow = '';
            row.dataset.customerId = customer.id;
            row.dataset.customerName = customer.name;
            row.dataset.phone = customer.phone;
            row.dataset.visits = customer.visits;
            row.dataset.lastVisit = customer.lastVisit;
            row.dataset.history = JSON.stringify(customer.history);
            row.className = 'transition hover:bg-cream/45';

            const nameCell = document.createElement('td');
            nameCell.className = 'px-4 py-4 sm:px-5';
            const name = document.createElement('div');
            name.className = 'font-medium text-ink';
            name.textContent = customer.name;
            const badge = document.createElement('span');
            badge.dataset.customerBadge = '';
            badge.className = `mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium ${customerBadgeStyle(customer.visits)}`;
            badge.textContent = customerBadgeText(customer.visits);
            nameCell.append(name, badge);
            row.appendChild(nameCell);

            [customer.phone, customer.visits, customer.lastVisit].forEach((value) => {
                const cell = document.createElement('td');
                cell.className = 'whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5';
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actionsCell = document.createElement('td');
            actionsCell.className = 'px-4 py-4 sm:px-5';
            const actions = document.createElement('div');
            actions.className = 'flex justify-end gap-2';

            [['view', 'View', 'text-maroon'], ['edit', 'Edit', 'text-ink']].forEach(([action, label, color]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.customerAction = action;
                button.className = `rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium transition hover:border-gold hover:bg-gold/10 ${color}`;
                button.textContent = label;
                actions.appendChild(button);
            });

            actionsCell.appendChild(actions);
            row.appendChild(actionsCell);

            return row;
        }

        function filterCustomers() {
            const searchTerm = customerSearch.value.trim().toLowerCase();
            let visibleCount = 0;

            document.querySelectorAll('[data-customer-row]').forEach((row) => {
                const matches = row.dataset.customerName.toLowerCase().includes(searchTerm);
                row.classList.toggle('hidden', !matches);
                visibleCount += matches ? 1 : 0;
            });

            document.getElementById('empty-customers').classList.toggle('hidden', visibleCount > 0);
        }

        function showCustomerHistory(history) {
            const historyContainer = document.getElementById('customer-history');
            historyContainer.replaceChildren();

            if (history.length === 0) {
                const emptyMessage = document.createElement('p');
                emptyMessage.className = 'p-5 text-sm text-ink/55';
                emptyMessage.textContent = 'No appointment history yet.';
                historyContainer.appendChild(emptyMessage);

                return;
            }

            history.forEach((appointment) => {
                const entry = document.createElement('article');
                entry.className = 'flex items-start gap-3 p-4';
                const marker = document.createElement('span');
                marker.className = 'mt-1 h-2 w-2 shrink-0 rounded-full bg-gold';
                marker.setAttribute('aria-hidden', 'true');

                const content = document.createElement('div');
                content.className = 'min-w-0 flex-1';
                const heading = document.createElement('div');
                heading.className = 'flex flex-wrap items-center justify-between gap-2';
                const service = document.createElement('h3');
                service.className = 'text-sm font-semibold text-ink';
                service.textContent = appointment.service;
                const date = document.createElement('span');
                date.className = 'text-xs text-ink/55';
                date.textContent = appointment.date;
                heading.append(service, date);

                const details = document.createElement('p');
                details.className = 'mt-1 text-xs text-ink/60';
                details.textContent = `${appointment.therapist} · ${appointment.status}`;
                content.append(heading, details);
                entry.append(marker, content);
                historyContainer.appendChild(entry);
            });
        }

        function selectCustomer(row) {
            selectedCustomerRow = row;
            document.querySelectorAll('[data-customer-row]').forEach((customerRow) => {
                customerRow.classList.toggle('bg-gold/10', customerRow === row);
            });

            const visits = Number(row.dataset.visits);
            const badge = document.getElementById('detail-customer-badge');
            const customerName = row.dataset.customerName;
            const initials = customerName.split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase();

            document.getElementById('customer-avatar').textContent = initials;
            document.getElementById('detail-customer-name').textContent = customerName;
            document.getElementById('detail-phone').textContent = row.dataset.phone;
            document.getElementById('detail-total-visits').textContent = visits;
            document.getElementById('detail-last-visit').textContent = row.dataset.lastVisit === '—' ? 'No visits yet' : row.dataset.lastVisit;
            badge.className = `mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${customerBadgeStyle(visits)}`;
            badge.textContent = customerBadgeText(visits);
            showCustomerHistory(JSON.parse(row.dataset.history));
        }

        function openCustomerEditor(row = null) {
            modalTrigger = document.activeElement;
            customerForm.reset();
            document.getElementById('customer-id').value = row?.dataset.customerId ?? '';
            document.getElementById('customer-modal-title').textContent = row ? 'Edit Customer' : 'Add Customer';
            document.getElementById('customer-name').value = row?.dataset.customerName ?? '';
            document.getElementById('customer-phone').value = row?.dataset.phone ?? '';
            setCustomerModalOpen(true);
        }

        document.getElementById('add-customer').addEventListener('click', () => openCustomerEditor());
        document.getElementById('close-customer-modal').addEventListener('click', () => setCustomerModalOpen(false));
        document.getElementById('cancel-customer').addEventListener('click', () => setCustomerModalOpen(false));
        customerSearch.addEventListener('input', filterCustomers);
        customerModal.addEventListener('click', (event) => {
            if (event.target === customerModal) {
                setCustomerModalOpen(false);
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !customerModal.classList.contains('hidden')) {
                setCustomerModalOpen(false);
            }
        });

        customerTableBody.addEventListener('click', (event) => {
            const actionButton = event.target.closest('[data-customer-action]');

            if (!actionButton) {
                return;
            }

            const row = actionButton.closest('[data-customer-row]');

            if (actionButton.dataset.customerAction === 'view') {
                selectCustomer(row);

                return;
            }

            openCustomerEditor(row);
        });

        customerForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const customerId = document.getElementById('customer-id').value;
            const currentRow = customerId
                ? customerTableBody.querySelector(`[data-customer-id="${customerId}"]`)
                : null;
            const customer = {
                id: customerId || nextCustomerId++,
                name: document.getElementById('customer-name').value.trim(),
                phone: document.getElementById('customer-phone').value.trim(),
                visits: currentRow?.dataset.visits ?? 0,
                lastVisit: currentRow?.dataset.lastVisit ?? '—',
                history: currentRow ? JSON.parse(currentRow.dataset.history) : [],
            };
            const newRow = createCustomerRow(customer);

            if (currentRow) {
                currentRow.replaceWith(newRow);
            } else {
                customerTableBody.appendChild(newRow);
            }

            customerSearch.value = '';
            filterCustomers();
            selectCustomer(newRow);
            setCustomerModalOpen(false);
        });

        selectCustomer(selectedCustomerRow);
    </script>
@endsection
