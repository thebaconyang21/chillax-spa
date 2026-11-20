@extends('layouts.app')

@section('title', 'Appointment Management')
@section('breadcrumb', 'Appointment Management')

@section('content')
    @php
        $appointments = [
            ['id' => 1, 'time' => '11:00 AM', 'customer' => 'Ana Reyes', 'phone' => '0917 555 0142', 'service' => 'Combo (Swedish + Add-on)', 'therapist' => 'Therapist A', 'status' => 'Completed'],
            ['id' => 2, 'time' => '1:00 PM', 'customer' => 'Maria Santos', 'phone' => '0917 123 4567', 'service' => 'Swedish Massage (60 mins)', 'therapist' => 'Therapist B', 'status' => 'Arrived'],
            ['id' => 3, 'time' => '2:00 PM', 'customer' => 'Carlo Reyes', 'phone' => '0918 555 0186', 'service' => 'Therapeutic Massage (90 mins)', 'therapist' => 'Therapist C', 'status' => 'In Service'],
            ['id' => 4, 'time' => '3:30 PM', 'customer' => 'Liza Cruz', 'phone' => '0920 444 7621', 'service' => 'Swedish Massage (60 mins)', 'therapist' => 'Therapist A', 'status' => 'Confirmed'],
            ['id' => 5, 'time' => '5:00 PM', 'customer' => 'John Dela Cruz', 'phone' => '0916 333 9081', 'service' => 'Foot Massage', 'therapist' => 'Therapist C', 'status' => 'Confirmed'],
            ['id' => 6, 'time' => '7:00 PM', 'customer' => 'Anna Garcia', 'phone' => '0915 222 6840', 'service' => 'Therapeutic Massage (90 mins)', 'therapist' => 'Therapist B', 'status' => 'Pending'],
        ];
        $statusStyles = [
            'Pending' => 'bg-amber-100 text-amber-800',
            'Confirmed' => 'bg-violet-100 text-violet-800',
            'Arrived' => 'bg-sky-100 text-sky-800',
            'In Service' => 'bg-indigo-100 text-indigo-800',
            'Completed' => 'bg-emerald-100 text-emerald-800',
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 2xl:flex-row 2xl:items-end">
        <div class="flex items-start gap-3">
            <span class="mt-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/></svg>
            </span>
            <div>
                <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Appointment Management</h1>
                <p class="mt-1 text-sm text-ink/65">Manage customer appointments and update their status.</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label class="relative">
                <span class="sr-only">Appointment date</span>
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-maroon/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                <input type="date" value="2026-11-20" class="min-h-11 w-full rounded-lg border border-maroon/15 bg-white py-2 pl-10 pr-3 text-sm text-ink shadow-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/30 sm:w-auto">
            </label>
            <button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New Appointment
            </button>
        </div>
    </div>

    <section class="mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-3 shadow-sm lg:flex-row lg:items-center" aria-label="Search and filter appointments">
        <label class="relative block min-w-0 flex-1">
            <span class="sr-only">Search customer name</span>
            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input id="appointment-search" type="search" placeholder="Search customer name..." class="h-11 w-full rounded-full border border-maroon/15 bg-white pl-10 pr-4 text-sm outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/30">
        </label>
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter appointments by status">
            @foreach (['All', 'Upcoming', 'Arrived', 'In Service', 'Completed'] as $filter)
                <button type="button" data-filter="{{ \Illuminate\Support\Str::slug($filter) }}" aria-pressed="{{ $filter === 'All' ? 'true' : 'false' }}" class="appointment-filter rounded-full px-4 py-2.5 text-xs font-medium transition {{ $filter === 'All' ? 'bg-maroon text-white' : 'bg-cream text-ink/75 hover:bg-gold/20' }}">
                    {{ $filter }}
                </button>
            @endforeach
        </div>
    </section>

    <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(320px,0.8fr)]">
        <div class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-maroon/10 px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01"/></svg>
                        <h2 class="text-base font-semibold text-ink">Today&apos;s Appointments <span class="font-normal text-ink/55">(Nov 20, 2026)</span></h2>
                    </div>
                    <span id="appointment-count" class="text-xs text-ink/55">{{ count($appointments) }} appointments</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[790px] text-left text-sm">
                        <thead class="bg-cream/75 text-xs text-ink/70">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Time</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Customer</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Service</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Therapist</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="appointment-rows" class="divide-y divide-maroon/5">
                            @foreach ($appointments as $appointment)
                                @php
                                    $appointmentDate = 'Nov 20, 2026 (Fri)';
                                    $statusKey = \Illuminate\Support\Str::slug($appointment['status']);
                                    $isSelected = $appointment['id'] === 2;
                                @endphp
                                <tr
                                    data-appointment-id="{{ $appointment['id'] }}"
                                    data-customer="{{ $appointment['customer'] }}"
                                    data-phone="{{ $appointment['phone'] }}"
                                    data-date="{{ $appointmentDate }}"
                                    data-time="{{ $appointment['time'] }}"
                                    data-service="{{ $appointment['service'] }}"
                                    data-therapist="{{ $appointment['therapist'] }}"
                                    data-status="{{ $statusKey }}"
                                    class="appointment-row {{ $isSelected ? 'bg-gold/10' : 'hover:bg-cream/45' }}">
                                    <td class="whitespace-nowrap px-4 py-3.5 font-medium text-ink">{{ $appointment['time'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-ink">{{ $appointment['customer'] }}</td>
                                    <td class="max-w-40 px-4 py-3.5 text-ink/80">{{ $appointment['service'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-ink/80">{{ $appointment['therapist'] }}</td>
                                    <td class="px-4 py-3.5">
                                        <span data-status-badge class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$appointment['status']] }}">
                                            <span class="mr-1.5 mt-1 h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                            {{ $appointment['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <button type="button" data-select-appointment class="inline-flex whitespace-nowrap items-center gap-1.5 rounded-lg border border-maroon/15 px-2.5 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="empty-appointments" class="hidden">
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-ink/55">No appointments match your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5">
                <h2 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <button type="button" class="quick-action">
                        <svg class="h-7 w-7 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        <span>New Appointment</span>
                    </button>
                    <button id="quick-search" type="button" class="quick-action">
                        <svg class="h-7 w-7 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        <span>Search Customer</span>
                    </button>
                    <button id="quick-update-status" type="button" class="quick-action">
                        <svg class="h-7 w-7 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01"/></svg>
                        <span>Update Status</span>
                    </button>
                    <button id="print-schedule" type="button" class="quick-action">
                        <svg class="h-7 w-7 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>
                        <span>Print Schedule</span>
                    </button>
                </div>
            </section>
        </div>

        <aside class="min-w-0 space-y-5">
            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="appointment-details-heading">
                <h2 id="appointment-details-heading" class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                    Appointment Details
                </h2>
                <div class="flex items-center gap-4 border-b border-maroon/10 pb-4">
                    <div id="detail-avatar" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gold/20 text-xl font-semibold text-maroon" aria-hidden="true">MS</div>
                    <div class="min-w-0">
                        <h3 id="detail-customer" class="truncate text-lg font-semibold text-ink">Maria Santos</h3>
                        <p class="mt-1 flex items-center gap-2 text-sm text-ink/65">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z"/></svg>
                            <span id="detail-phone">0917 123 4567</span>
                        </p>
                    </div>
                </div>

                <dl class="space-y-3 border-b border-maroon/10 py-4 text-sm">
                    <div class="flex items-start gap-3">
                        <dt class="flex w-1/2 items-center gap-2 text-ink/65"><svg class="h-4 w-4 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>Appointment Date</dt>
                        <dd id="detail-date" class="flex-1 font-medium text-ink">Nov 20, 2026 (Fri)</dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex w-1/2 items-center gap-2 text-ink/65"><svg class="h-4 w-4 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>Appointment Time</dt>
                        <dd id="detail-time" class="flex-1 font-medium text-ink">1:00 PM</dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex w-1/2 items-center gap-2 text-ink/65"><svg class="h-4 w-4 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21c-4-3-7-6-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4-3 7-7 10Z"/></svg>Service</dt>
                        <dd id="detail-service" class="flex-1 font-medium text-ink">Swedish Massage (60 mins)</dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex w-1/2 items-center gap-2 text-ink/65"><svg class="h-4 w-4 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>Therapist</dt>
                        <dd id="detail-therapist" class="flex-1 font-medium text-ink">Therapist B</dd>
                    </div>
                </dl>

                <div class="pt-4">
                    <label for="detail-status" class="mb-2 block text-sm font-semibold text-ink">Status</label>
                    <select id="detail-status" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm font-medium text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/30">
                        @foreach (['Pending', 'Confirmed', 'Arrived', 'In Service', 'Completed'] as $status)
                            <option value="{{ \Illuminate\Support\Str::slug($status) }}" {{ $status === 'Arrived' ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-white transition hover:bg-maroon/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                    Proceed to POS <span aria-hidden="true">&rarr;</span>
                </button>
            </section>

            <section class="rounded-xl border border-gold/40 bg-gold/10 p-4 sm:p-5">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="flex items-center gap-2 text-sm font-semibold text-ink">
                        <svg class="h-5 w-5 text-gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6v-5a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v5l-2 2v1h16v-1l-2-2Z"/></svg>
                        Upcoming Reminder
                    </h2>
                    <a href="#" class="text-xs font-medium text-maroon hover:text-gold">View All <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="mt-3 flex items-start gap-3 rounded-lg bg-white/80 p-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-ink">3:30 PM &middot; Liza Cruz</p>
                        <p class="mt-1 text-xs leading-relaxed text-ink/65">Swedish Massage (60 mins) &middot; Therapist A</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-gold/20 px-2 py-1 text-[10px] font-medium text-maroon">In 30 mins</span>
                </div>
            </section>
        </aside>
    </div>

    <style>
        .quick-action {
            display: flex;
            min-height: 7rem;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            border: 1px solid rgb(107 15 26 / 10%);
            border-radius: 0.75rem;
            background: rgb(251 245 234 / 70%);
            padding: 1rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            transition: background-color 150ms, border-color 150ms;
        }

        .quick-action:hover {
            border-color: #d4a017;
            background: rgb(212 160 23 / 10%);
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #appointment-rows,
            #appointment-rows * {
                visibility: visible;
            }

            #appointment-rows {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>

    <script>
        const appointmentRows = [...document.querySelectorAll('.appointment-row')];
        const appointmentSearch = document.getElementById('appointment-search');
        const filterButtons = [...document.querySelectorAll('.appointment-filter')];
        const statusStyles = {
            pending: 'bg-amber-100 text-amber-800',
            confirmed: 'bg-violet-100 text-violet-800',
            arrived: 'bg-sky-100 text-sky-800',
            'in-service': 'bg-indigo-100 text-indigo-800',
            completed: 'bg-emerald-100 text-emerald-800',
        };
        let activeFilter = 'all';
        let selectedRow = document.querySelector('.appointment-row[data-appointment-id="2"]');

        function applyAppointmentFilters() {
            const searchTerm = appointmentSearch.value.trim().toLowerCase();
            let visibleCount = 0;

            appointmentRows.forEach((row) => {
                const matchesSearch = row.dataset.customer.toLowerCase().includes(searchTerm);
                const matchesFilter = activeFilter === 'all'
                    || (activeFilter === 'upcoming' && ['pending', 'confirmed'].includes(row.dataset.status))
                    || row.dataset.status === activeFilter;
                const isVisible = matchesSearch && matchesFilter;

                row.classList.toggle('hidden', !isVisible);
                visibleCount += isVisible ? 1 : 0;
            });

            document.getElementById('empty-appointments').classList.toggle('hidden', visibleCount > 0);
            document.getElementById('appointment-count').textContent = `${visibleCount} ${visibleCount === 1 ? 'appointment' : 'appointments'}`;
        }

        function selectAppointment(row) {
            selectedRow = row;
            appointmentRows.forEach((appointmentRow) => {
                appointmentRow.classList.toggle('bg-gold/10', appointmentRow === row);
                appointmentRow.classList.toggle('hover:bg-cream/45', appointmentRow !== row);
            });

            document.getElementById('detail-avatar').textContent = row.dataset.customer
                .split(' ')
                .map((namePart) => namePart[0])
                .join('')
                .slice(0, 2)
                .toUpperCase();
            document.getElementById('detail-customer').textContent = row.dataset.customer;
            document.getElementById('detail-phone').textContent = row.dataset.phone;
            document.getElementById('detail-date').textContent = row.dataset.date;
            document.getElementById('detail-time').textContent = row.dataset.time;
            document.getElementById('detail-service').textContent = row.dataset.service;
            document.getElementById('detail-therapist').textContent = row.dataset.therapist;
            document.getElementById('detail-status').value = row.dataset.status;
        }

        function updateStatusBadge(row, status) {
            const badge = row.querySelector('[data-status-badge]');
            const label = status.replace('-', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

            badge.className = `inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ${statusStyles[status]}`;
            badge.innerHTML = `<span class="mr-1.5 mt-1 h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>${label}`;
            row.dataset.status = status;
        }

        appointmentSearch.addEventListener('input', applyAppointmentFilters);
        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.filter;
                filterButtons.forEach((filterButton) => {
                    const isActive = filterButton === button;

                    filterButton.setAttribute('aria-pressed', String(isActive));
                    filterButton.classList.toggle('bg-maroon', isActive);
                    filterButton.classList.toggle('text-white', isActive);
                    filterButton.classList.toggle('bg-cream', !isActive);
                    filterButton.classList.toggle('text-ink/75', !isActive);
                });
                applyAppointmentFilters();
            });
        });

        document.getElementById('appointment-rows').addEventListener('click', (event) => {
            const selectionButton = event.target.closest('[data-select-appointment]');

            if (selectionButton) {
                selectAppointment(selectionButton.closest('.appointment-row'));
            }
        });

        document.getElementById('detail-status').addEventListener('change', (event) => {
            updateStatusBadge(selectedRow, event.target.value);
            applyAppointmentFilters();
        });
        document.getElementById('quick-search').addEventListener('click', () => appointmentSearch.focus());
        document.getElementById('quick-update-status').addEventListener('click', () => document.getElementById('detail-status').focus());
        document.getElementById('print-schedule').addEventListener('click', () => window.print());

        selectAppointment(selectedRow);
    </script>
@endsection
