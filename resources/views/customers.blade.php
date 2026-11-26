@extends('layouts.app')

@section('title', 'Customer Management')
@section('breadcrumb', 'Customer Management')

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
                <form method="GET" action="{{ route('customers') }}" class="flex w-full gap-2 sm:max-w-sm">
                    <label class="relative block min-w-0 flex-1">
                        <span class="sr-only">Search customers</span>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        <input id="customer-search" name="search" type="search" value="{{ $search }}" placeholder="Search name or number..." class="h-11 w-full rounded-full border border-maroon/15 bg-white pl-10 pr-4 text-sm outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/30">
                    </label>
                    <button type="submit" class="rounded-full bg-maroon px-4 text-sm font-medium text-white transition hover:bg-maroon/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Search</button>
                </form>
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
                        @forelse ($customers as $customer)
                            @php
                                $isReturningCustomer = $customer->total_visits >= 1;
                                $badgeStyle = $isReturningCustomer ? 'bg-emerald-50 text-emerald-700' : 'bg-gold/15 text-maroon';
                                $lastVisit = $customer->last_visit
                                    ? \Illuminate\Support\Carbon::parse($customer->last_visit)->format('M j, Y')
                                    : '-';
                            @endphp
                            <tr
                                data-customer-row
                                data-customer-id="{{ $customer->id }}"
                                data-customer-name="{{ $customer->customer_name }}"
                                data-contact-number="{{ $customer->contact_number }}"
                                data-show-url="{{ route('customers.show', $customer) }}"
                                data-update-url="{{ route('customers.update', $customer) }}"
                                class="transition hover:bg-cream/45">
                                <td class="px-4 py-4 sm:px-5">
                                    <div class="font-medium text-ink">{{ $customer->customer_name }}</div>
                                    <span data-customer-badge class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badgeStyle }}">
                                        {{ $isReturningCustomer ? 'Returning Customer' : 'New Customer' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $customer->contact_number ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $customer->total_visits }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-ink/75 sm:px-5">{{ $lastVisit }}</td>
                                <td class="px-4 py-4 sm:px-5">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-customer-action="view" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">View</button>
                                        <button type="button" data-customer-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm-delete data-record-name="{{ $customer->customer_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-ink/55">No customers match your search.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col justify-between gap-3 border-t border-maroon/10 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
                <p class="text-xs text-ink/60">
                    Showing {{ $customers->firstItem() ?? 0 }}–{{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                </p>
                @if ($customers->hasPages())
                    <nav class="flex flex-wrap items-center justify-end gap-1" aria-label="Customer pagination">
                        @if ($customers->onFirstPage())
                            <span class="rounded-lg border border-maroon/10 px-3 py-2 text-xs text-ink/35" aria-disabled="true">Previous</span>
                        @else
                            <a href="{{ $customers->previousPageUrl() }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Previous</a>
                        @endif

                        @foreach ($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                            <a href="{{ $url }}" @if ($page === $customers->currentPage()) aria-current="page" @endif class="min-w-9 rounded-lg border px-3 py-2 text-center text-xs font-medium transition {{ $page === $customers->currentPage() ? 'border-maroon bg-maroon text-white' : 'border-maroon/15 text-maroon hover:border-gold hover:bg-gold/10' }}">{{ $page }}</a>
                        @endforeach

                        @if ($customers->hasMorePages())
                            <a href="{{ $customers->nextPageUrl() }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Next</a>
                        @else
                            <span class="rounded-lg border border-maroon/10 px-3 py-2 text-xs text-ink/35" aria-disabled="true">Next</span>
                        @endif
                    </nav>
                @endif
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
                        <div id="customer-avatar" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gold/20 text-lg font-semibold text-maroon" aria-hidden="true">?</div>
                        <div class="min-w-0">
                            <h3 id="detail-customer-name" class="truncate text-lg font-semibold text-ink">Select a customer</h3>
                            <p id="detail-phone" class="mt-1 text-sm text-ink/65">-</p>
                            <span id="detail-customer-badge" class="mt-2 inline-flex rounded-full bg-gold/15 px-2.5 py-1 text-xs font-medium text-maroon">New Customer</span>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 py-4">
                        <div class="rounded-lg bg-cream/70 p-3">
                            <dt class="text-xs text-ink/55">Total Visits</dt>
                            <dd id="detail-total-visits" class="mt-1 text-lg font-semibold text-maroon">0</dd>
                        </div>
                        <div class="rounded-lg bg-cream/70 p-3">
                            <dt class="text-xs text-ink/55">Last Visit</dt>
                            <dd id="detail-last-visit" class="mt-1 text-sm font-semibold text-ink">-</dd>
                        </div>
                    </dl>
                    <p id="customer-detail-error" class="hidden text-sm text-red-700" role="alert"></p>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="customer-history-heading">
                <div class="border-b border-maroon/10 px-5 py-4">
                    <h2 id="customer-history-heading" class="font-display text-3xl text-maroon">Appointment History</h2>
                    <p class="mt-1 text-xs text-ink/55">Previous visits and services</p>
                </div>
                <div id="customer-history" class="divide-y divide-maroon/5">
                    <p class="p-5 text-sm text-ink/55">No history yet</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="customer-transactions-heading">
                <div class="border-b border-maroon/10 px-5 py-4">
                    <h2 id="customer-transactions-heading" class="font-display text-3xl text-maroon">Transaction History</h2>
                    <p class="mt-1 text-xs text-ink/55">Latest paid transactions</p>
                </div>
                <div id="customer-transactions" class="divide-y divide-maroon/5">
                    <p class="p-5 text-sm text-ink/55">No history yet</p>
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
            <form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                @method('PUT')
                <input id="customer-id" name="customer_id" type="hidden" value="{{ old('customer_id') }}">
                <div>
                    <label for="customer-name" class="mb-1.5 block text-sm font-medium text-ink">Customer Name</label>
                    <input id="customer-name" name="customer_name" type="text" value="{{ old('customer_name') }}" required maxlength="100" autocomplete="name" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="customer-phone" class="mb-1.5 block text-sm font-medium text-ink">Contact Number</label>
                    <input id="customer-phone" name="contact_number" type="tel" value="{{ old('contact_number') }}" maxlength="13" autocomplete="tel" placeholder="e.g. 09171234567" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
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
        const updateUrlTemplate = @js(route('customers.update', ['customer' => '__CUSTOMER__']));

        function setCustomerModalOpen(isOpen) {
            customerModal.classList.toggle('hidden', !isOpen);
            customerModal.classList.toggle('flex', isOpen);
            customerModal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                document.getElementById('customer-name').focus();
            }
        }

        function setMethodOverride(isEditing) {
            let methodInput = customerForm.querySelector('input[name="_method"]');

            if (isEditing) {
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    customerForm.appendChild(methodInput);
                }

                methodInput.value = 'PUT';
            } else if (methodInput) {
                methodInput.remove();
            }
        }

        function customerBadgeText(visits) {
            return Number(visits) >= 1 ? 'Returning Customer' : 'New Customer';
        }

        function customerBadgeStyle(visits) {
            return Number(visits) >= 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-gold/15 text-maroon';
        }

        function findCustomerRow(id) {
            return [...document.querySelectorAll('[data-customer-row]')]
                .find((row) => row.dataset.customerId === String(id));
        }

        function openCustomerEditor(customer = null) {
            customerForm.reset();
            document.getElementById('customer-id').value = customer?.id ?? '';
            document.getElementById('customer-modal-title').textContent = customer ? 'Edit Customer' : 'Add Customer';
            document.getElementById('customer-name').value = customer?.name ?? '';
            document.getElementById('customer-phone').value = customer?.phone ?? '';
            customerForm.action = customer
                ? updateUrlTemplate.replace('__CUSTOMER__', encodeURIComponent(customer.id))
                : @js(route('customers.store'));
            setMethodOverride(Boolean(customer));
            setCustomerModalOpen(true);
        }

        function addEmptyMessage(container, text = 'No history yet') {
            const message = document.createElement('p');
            message.className = 'p-5 text-sm text-ink/55';
            message.textContent = text;
            container.appendChild(message);
        }

        function setCustomerBadge(element, visits) {
            element.className = `mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${customerBadgeStyle(visits)}`;
            element.textContent = customerBadgeText(visits);
        }

        function renderAppointmentHistory(appointments) {
            const container = document.getElementById('customer-history');
            container.replaceChildren();

            if (appointments.length === 0) {
                addEmptyMessage(container);
                return;
            }

            appointments.forEach((appointment) => {
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
                service.textContent = appointment.service_name;

                const date = document.createElement('span');
                date.className = 'text-xs text-ink/55';
                date.textContent = `${appointment.date} · ${appointment.time}`;
                heading.append(service, date);

                const details = document.createElement('p');
                details.className = 'mt-1 text-xs text-ink/60';
                details.textContent = `${appointment.therapist_name} · ${appointment.status.replace(/_/g, ' ')}`;
                content.append(heading, details);
                entry.append(marker, content);
                container.appendChild(entry);
            });
        }

        function formatCurrency(amount) {
            return `₱${Number(amount).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })}`;
        }

        function renderTransactionHistory(transactions) {
            const container = document.getElementById('customer-transactions');
            container.replaceChildren();

            if (transactions.length === 0) {
                addEmptyMessage(container);
                return;
            }

            transactions.forEach((transaction) => {
                const entry = document.createElement('article');
                entry.className = 'flex items-start justify-between gap-3 p-4';

                const details = document.createElement('div');
                details.className = 'min-w-0';
                const date = document.createElement('h3');
                date.className = 'text-sm font-semibold text-ink';
                date.textContent = transaction.date;
                const method = document.createElement('p');
                method.className = 'mt-1 text-xs text-ink/60';
                method.textContent = transaction.payment_method === 'ecash' ? 'E-cash' : 'Cash';
                details.append(date, method);

                const amount = document.createElement('span');
                amount.className = 'whitespace-nowrap text-sm font-semibold text-maroon';
                amount.textContent = formatCurrency(transaction.total_amount);

                entry.append(details, amount);
                container.appendChild(entry);
            });
        }

        async function loadCustomerDetails(row) {
            document.querySelectorAll('[data-customer-row]').forEach((customerRow) => {
                customerRow.classList.toggle('bg-gold/10', customerRow === row);
            });

            const errorMessage = document.getElementById('customer-detail-error');
            errorMessage.classList.add('hidden');

            try {
                const response = await fetch(row.dataset.showUrl, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(`Customer details request failed with status ${response.status}.`);
                }

                const data = await response.json();
                const visits = Number(data.total_visits);
                const customerName = data.customer.customer_name;
                const initials = customerName.trim().split(/\s+/).map((part) => part[0]).join('').slice(0, 2).toUpperCase();

                document.getElementById('customer-avatar').textContent = initials;
                document.getElementById('detail-customer-name').textContent = customerName;
                document.getElementById('detail-phone').textContent = data.customer.contact_number || '-';
                document.getElementById('detail-total-visits').textContent = visits;
                document.getElementById('detail-last-visit').textContent = data.last_visit || '-';
                setCustomerBadge(document.getElementById('detail-customer-badge'), visits);
                renderAppointmentHistory(data.appointments);
                renderTransactionHistory(data.transactions);
            } catch (error) {
                console.error(error);
                errorMessage.textContent = 'Unable to load customer details. Please try again.';
                errorMessage.classList.remove('hidden');
            }
        }

        document.getElementById('add-customer').addEventListener('click', () => openCustomerEditor());
        document.getElementById('close-customer-modal').addEventListener('click', () => setCustomerModalOpen(false));
        document.getElementById('cancel-customer').addEventListener('click', () => setCustomerModalOpen(false));
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
                loadCustomerDetails(row);
                return;
            }

            openCustomerEditor({
                id: row.dataset.customerId,
                name: row.dataset.customerName,
                phone: row.dataset.contactNumber,
            });
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (form.matches('[data-confirm-delete]') && !window.confirm(`Delete "${form.dataset.recordName}"?`)) {
                event.preventDefault();
            }
        });

        @if ($errors->hasAny(['customer_name', 'contact_number']))
            const failedCustomerId = @js(old('customer_id'));
            const failedCustomerRow = findCustomerRow(failedCustomerId);
            const failedCustomer = failedCustomerRow
                ? {
                    id: failedCustomerRow.dataset.customerId,
                    name: failedCustomerRow.dataset.customerName,
                    phone: failedCustomerRow.dataset.contactNumber,
                }
                : (failedCustomerId ? { id: failedCustomerId, name: '', phone: '' } : null);

            openCustomerEditor(failedCustomer);
            document.getElementById('customer-name').value = @js(old('customer_name', ''));
            document.getElementById('customer-phone').value = @js(old('contact_number', ''));
        @elseif (request()->boolean('new'))
            openCustomerEditor();
        @endif

        const firstCustomerRow = document.querySelector('[data-customer-row]');

        if (firstCustomerRow) {
            loadCustomerDetails(firstCustomerRow);
        }
    </script>
@endsection
