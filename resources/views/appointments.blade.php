@extends('layouts.app')

@section('title', 'Appointment Management')
@section('breadcrumb', 'Appointment Management')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'arrived' => 'Arrived',
            'in_service' => 'In Service',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        $statusStyles = [
            'pending' => 'bg-orange-100 text-orange-800',
            'confirmed' => 'bg-violet-100 text-violet-800',
            'arrived' => 'bg-sky-100 text-sky-800',
            'in_service' => 'bg-teal-100 text-teal-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-gray-100 text-gray-700',
        ];
        $statusFilters = [
            'all' => 'All',
            'upcoming' => 'Upcoming',
            'arrived' => 'Arrived',
            'in_service' => 'In Service',
            'completed' => 'Completed',
        ];
        $isToday = $date === today()->toDateString();
    @endphp

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="appointment-print-header" class="mb-6 flex flex-col justify-between gap-4 2xl:flex-row 2xl:items-end">
        <div class="flex items-start gap-3">
            <span class="no-print mt-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/></svg>
            </span>
            <div>
                <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">
                    {{ $isToday ? "Today's Appointments" : 'Appointments ('.\Illuminate\Support\Carbon::parse($date)->format('M j, Y').')' }}
                </h1>
                <p id="print-date" class="hidden">Date: {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}</p>
                <p class="mt-1 text-sm text-ink/65 no-print">Manage customer appointments and update their status.</p>
            </div>
        </div>

        <div class="no-print flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('appointments') }}" id="appointment-date-form">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <label class="relative">
                    <span class="sr-only">Appointment date</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-maroon/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                    <input id="appointment-date" name="date" type="date" value="{{ $date }}" class="min-h-11 w-full rounded-lg border border-maroon/15 bg-white py-2 pl-10 pr-3 text-sm text-ink shadow-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/30 sm:w-auto">
                </label>
            </form>
            <button type="button" data-open-appointment-modal class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New Appointment
            </button>
        </div>
    </div>

    <section class="no-print mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-3 shadow-sm lg:flex-row lg:items-center" aria-label="Search and filter appointments">
        <form method="GET" action="{{ route('appointments') }}" class="flex min-w-0 flex-1 items-center gap-2">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <label class="relative block min-w-0 flex-1">
                <span class="sr-only">Search customer name</span>
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input id="appointment-search" name="search" type="search" value="{{ $search }}" placeholder="Search customer name..." class="h-11 w-full rounded-full border border-maroon/15 bg-white pl-10 pr-4 text-sm outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/30">
            </label>
            <button type="submit" class="rounded-full bg-maroon px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-maroon/90">Search</button>
        </form>
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter appointments by status">
            @foreach ($statusFilters as $filterKey => $filterLabel)
                <a
                    href="{{ route('appointments', ['date' => $date, 'status' => $filterKey, 'search' => $search]) }}"
                    aria-current="{{ $status === $filterKey ? 'page' : 'false' }}"
                    class="rounded-full px-4 py-2.5 text-xs font-medium transition {{ $status === $filterKey ? 'bg-maroon text-white' : 'bg-cream text-ink/75 hover:bg-gold/20' }}">
                    {{ $filterLabel }}
                </a>
            @endforeach
        </div>
    </section>

    <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(320px,0.8fr)]">
        <div class="min-w-0 space-y-5">
            <section id="appointments-table-section" class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
                <div class="no-print flex flex-wrap items-center justify-between gap-2 border-b border-maroon/10 px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/></svg>
                        <h2 class="text-base font-semibold text-ink">
                            {{ $isToday ? "Today's Appointments" : 'Appointments ('.\Illuminate\Support\Carbon::parse($date)->format('M j, Y').')' }}
                        </h2>
                    </div>
                    <span id="appointment-count" class="text-xs text-ink/55">{{ $appointments->count() }} {{ \Illuminate\Support\Str::plural('appointment', $appointments->count()) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table id="appointments-table" class="w-full min-w-[790px] text-left text-sm">
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
                            @forelse ($appointments as $appointment)
                                <tr
                                    data-appointment-row
                                    data-appointment-id="{{ $appointment->id }}"
                                    data-customer-id="{{ $appointment->customer_id }}"
                                    data-customer="{{ $appointment->customer->customer_name }}"
                                    data-phone="{{ $appointment->customer->contact_number }}"
                                    data-date="{{ $appointment->appointment_date }}"
                                    data-time="{{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('H:i') }}"
                                    data-service-id="{{ $appointment->service_id }}"
                                    data-service="{{ $appointment->service->service_name }}"
                                    data-duration="{{ $appointment->service->duration }}"
                                    data-therapist-id="{{ $appointment->therapist_id }}"
                                    data-therapist="{{ $appointment->therapist->therapist_name }}"
                                    data-status="{{ $appointment->appointment_status }}"
                                    data-show-url="{{ route('appointments.show', $appointment) }}"
                                    data-update-url="{{ route('appointments.update', $appointment) }}"
                                    class="appointment-row cursor-pointer transition hover:bg-cream/45"
                                    tabindex="0">
                                    <td class="whitespace-nowrap px-4 py-3.5 font-medium text-ink">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-ink">{{ $appointment->customer->customer_name }}</td>
                                    <td class="max-w-40 px-4 py-3.5 text-ink/80">{{ $appointment->service->service_name }} ({{ $appointment->service->duration }} mins)</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-ink/80">{{ $appointment->therapist->therapist_name }}</td>
                                    <td class="px-4 py-3.5">
                                        <span data-status-badge class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$appointment->appointment_status] }}">
                                            <span class="mr-1.5 mt-1 h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                            {{ $statusLabels[$appointment->appointment_status] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if ($appointment->appointment_status === 'arrived')
                                            <a href="{{ route('pos', ['appointment' => $appointment->id]) }}" class="inline-flex whitespace-nowrap items-center gap-1.5 rounded-lg bg-maroon px-2.5 py-2 text-xs font-semibold text-white transition hover:bg-maroon/90">Proceed to POS</a>
                                        @else
                                            <button type="button" data-view-appointment class="inline-flex whitespace-nowrap items-center gap-1.5 rounded-lg border border-maroon/15 px-2.5 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                View
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-ink/55">No appointments for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="no-print rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5">
                <h2 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <button type="button" data-open-appointment-modal class="quick-action">
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

        <aside class="no-print min-w-0 space-y-5">
            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="appointment-details-heading">
                <h2 id="appointment-details-heading" class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <svg class="h-5 w-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                    Appointment Details
                </h2>
                <div id="appointment-details-empty" class="rounded-lg bg-cream/60 px-4 py-8 text-center text-sm text-ink/55">
                    Select an appointment to view details.
                </div>
                <div id="appointment-details-content" class="hidden">
                    <div class="flex items-center gap-4 border-b border-maroon/10 pb-4">
                        <div id="detail-avatar" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gold/20 text-xl font-semibold text-maroon" aria-hidden="true"></div>
                        <div class="min-w-0">
                            <h3 id="detail-customer" class="truncate text-lg font-semibold text-ink"></h3>
                            <p class="mt-1 flex items-center gap-2 text-sm text-ink/65">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z"/></svg>
                                <span id="detail-phone"></span>
                            </p>
                            <span id="detail-customer-type" class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"></span>
                        </div>
                    </div>

                    <dl class="space-y-3 border-b border-maroon/10 py-4 text-sm">
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Appointment Date</dt>
                            <dd id="detail-date" class="flex-1 font-medium text-ink"></dd>
                        </div>
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Appointment Time</dt>
                            <dd id="detail-time" class="flex-1 font-medium text-ink"></dd>
                        </div>
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Ends At</dt>
                            <dd id="detail-end-time" class="flex-1 font-medium text-ink"></dd>
                        </div>
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Service</dt>
                            <dd id="detail-service" class="flex-1 font-medium text-ink"></dd>
                        </div>
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Therapist</dt>
                            <dd id="detail-therapist" class="flex-1 font-medium text-ink"></dd>
                        </div>
                        <div class="flex items-start gap-3">
                            <dt class="w-1/2 text-ink/65">Reserved Price</dt>
                            <dd id="detail-price" class="flex-1 font-semibold text-maroon"></dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-ink">Current Status</span>
                        <span id="detail-current-status" class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"></span>
                    </div>
                    <form id="detail-status-form" method="POST" action="#" class="pt-4">
                        @csrf
                        @method('PATCH')
                        <label for="detail-status" class="mb-2 block text-sm font-semibold text-ink">Status</label>
                        <select id="detail-status" name="status" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm font-medium text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/30"></select>
                        <p id="detail-terminal-status" class="hidden text-sm text-ink/60"></p>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <button id="save-status" type="submit" class="min-h-10 rounded-lg bg-maroon px-3 py-2 text-sm font-semibold text-white transition hover:bg-maroon/90">Update Status</button>
                            <button id="edit-appointment" type="button" class="hidden min-h-10 rounded-lg border border-maroon/15 px-3 py-2 text-sm font-semibold text-ink transition hover:border-gold hover:bg-gold/10">Edit Appointment</button>
                        </div>
                    </form>

                    <div id="ready-for-pos" class="mt-4 hidden rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                        <p class="text-sm font-semibold text-emerald-800">Ready for POS</p>
                        <p class="mt-1 text-xs text-emerald-700">This customer has arrived and is ready to check out.</p>
                    </div>
                    <a id="detail-pos-link" href="#" class="mt-4 hidden min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-white transition hover:bg-maroon/90">
                        Proceed to POS <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
                <p id="appointment-detail-error" class="mt-3 hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-800" role="alert"></p>
            </section>

            @if ($upcomingReminder)
                <section class="rounded-xl border border-gold/40 bg-gold/10 p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="flex items-center gap-2 text-sm font-semibold text-ink">
                            <svg class="h-5 w-5 text-gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6v-5a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v5l-2 2v1h16v-1l-2-2Z"/></svg>
                            Upcoming Reminder
                        </h2>
                        <button type="button" data-reminder-appointment="{{ $upcomingReminder->id }}" class="text-xs font-medium text-maroon hover:text-gold">View <span aria-hidden="true">&rarr;</span></button>
                    </div>
                    <div class="mt-3 flex items-start gap-3 rounded-lg bg-white/80 p-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-ink">{{ \Illuminate\Support\Carbon::parse($upcomingReminder->appointment_time)->format('g:i A') }} &middot; {{ $upcomingReminder->customer->customer_name }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-ink/65">{{ $upcomingReminder->service->service_name }} ({{ $upcomingReminder->service->duration }} mins) &middot; {{ $upcomingReminder->therapist->therapist_name }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-gold/20 px-2 py-1 text-[10px] font-medium text-maroon">In {{ $minutesUntilReminder }} {{ \Illuminate\Support\Str::plural('min', $minutesUntilReminder) }}</span>
                    </div>
                </section>
            @endif
        </aside>
    </div>

    <div id="appointment-modal" class="no-print fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-2xl rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="appointment-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="appointment-modal-title" class="text-lg font-semibold text-maroon">New Appointment</h2>
                    <p class="mt-1 text-xs text-ink/55">Choose a customer, service, time, and available therapist.</p>
                </div>
                <button id="close-appointment-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="appointment-form" method="POST" action="{{ route('appointments.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input id="appointment-record-id" name="appointment_record_id" type="hidden" value="{{ old('appointment_record_id') }}">
                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="appointment-customer" class="text-sm font-medium text-ink">Customer</label>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-ink/70">
                            <input id="new-customer-toggle" type="checkbox" class="rounded border-maroon/30 text-maroon focus:ring-gold">
                            Add a new customer
                        </label>
                    </div>
                    <div id="existing-customer-field">
                        <select id="appointment-customer" name="customer_id" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            <option value="">Select a customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}{{ $customer->contact_number ? ' · '.$customer->contact_number : '' }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                    </div>
                    <div id="new-customer-fields" class="hidden grid gap-3 sm:grid-cols-2">
                        <label>
                            <span class="mb-1 block text-xs font-medium text-ink/65">Customer name</span>
                            <input id="new-customer-name" name="new_customer_name" type="text" maxlength="100" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            @error('new_customer_name')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="mb-1 block text-xs font-medium text-ink/65">Contact number (optional)</span>
                            <input id="new-customer-contact" name="new_customer_contact" type="tel" placeholder="e.g. 09171234567" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            @error('new_customer_contact')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                        </label>
                    </div>
                </div>

                <div>
                    <label for="appointment-service" class="mb-1.5 block text-sm font-medium text-ink">Service</label>
                    <select id="appointment-service" name="service_id" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        <option value="">Select a service</option>
                        @foreach ($services as $service)
                            <option
                                value="{{ $service->id }}"
                                data-duration="{{ $service->duration }}"
                                data-happy-hour-price="{{ $service->happy_hour_price ?? '' }}"
                                data-regular-price="{{ $service->regular_price ?? '' }}">
                                {{ $service->service_name }} ({{ $service->duration }} mins)
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                    <p id="appointment-price" class="mt-2 hidden text-sm font-semibold text-maroon"></p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label>
                        <span class="mb-1.5 block text-sm font-medium text-ink">Appointment Date</span>
                        <input id="appointment-form-date" name="appointment_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('appointment_date', $date) }}" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        @error('appointment_date')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label>
                        <span class="mb-1.5 block text-sm font-medium text-ink">Appointment Time</span>
                        <input id="appointment-form-time" name="appointment_time" type="time" value="{{ old('appointment_time') }}" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        @error('appointment_time')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                    </label>
                </div>

                <div>
                    <label for="appointment-therapist" class="mb-1.5 block text-sm font-medium text-ink">Available Therapist</label>
                    <select id="appointment-therapist" name="therapist_id" required disabled class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25 disabled:cursor-not-allowed disabled:bg-cream">
                        <option value="">Choose a service, date, and time first</option>
                    </select>
                    <p id="therapist-availability-message" class="mt-1 hidden text-xs text-ink/60"></p>
                    @error('therapist_id')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-maroon/10 pt-4 sm:flex-row sm:justify-end">
                    <button id="cancel-appointment" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Appointment</button>
                </div>
            </form>
        </section>
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
            background: #fff;
            padding: 1rem;
            color: #2f2526;
            font-size: 0.75rem;
            font-weight: 600;
            transition: background-color 150ms, border-color 150ms;
        }

        .quick-action:hover {
            border-color: #d4a017;
            background: rgb(212 160 23 / 10%);
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            #appointment-print-header,
            #appointment-print-header *,
            #appointments-table-section,
            #appointments-table-section * {
                visibility: visible !important;
            }

            #print-date {
                display: block !important;
            }

            #appointment-print-header {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                margin: 0;
            }

            #appointments-table-section {
                position: absolute;
                top: 5rem;
                left: 0;
                width: 100%;
                overflow: visible !important;
                border: 0;
                box-shadow: none;
            }

            #appointments-table {
                width: 100% !important;
                min-width: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <script>
        const appointmentRows = [...document.querySelectorAll('[data-appointment-row]')];
        const appointmentTable = document.getElementById('appointment-rows');
        const appointmentSearch = document.getElementById('appointment-search');
        const appointmentModal = document.getElementById('appointment-modal');
        const appointmentForm = document.getElementById('appointment-form');
        const therapistSelect = document.getElementById('appointment-therapist');
        const happyHourStart = @json(config('spa.happy_hour_start'));
        const happyHourEnd = @json(config('spa.happy_hour_end'));
        const availabilityUrl = @json(route('appointments.available-therapists'));
        const appointmentStoreUrl = @json(route('appointments.store'));
        const appointmentUpdateUrl = @json(route('appointments.update', ['appointment' => '__APPOINTMENT__']));
        const appointmentShowUrl = @json(route('appointments.show', ['appointment' => '__APPOINTMENT__']));
        const appointmentStatusUrl = @json(route('appointments.status', ['appointment' => '__APPOINTMENT__']));
        const posUrl = @json(route('pos', ['appointment' => '__APPOINTMENT__']));
        const statusLabels = {
            pending: 'Pending',
            confirmed: 'Confirmed',
            arrived: 'Arrived',
            in_service: 'In Service',
            completed: 'Completed',
            cancelled: 'Cancelled',
        };
        const statusStyles = {
            pending: 'bg-orange-100 text-orange-800',
            confirmed: 'bg-violet-100 text-violet-800',
            arrived: 'bg-sky-100 text-sky-800',
            in_service: 'bg-teal-100 text-teal-800',
            completed: 'bg-emerald-100 text-emerald-800',
            cancelled: 'bg-gray-100 text-gray-700',
        };
        const transitions = {
            pending: ['confirmed', 'arrived', 'cancelled'],
            confirmed: ['arrived', 'cancelled'],
            arrived: ['in_service', 'cancelled'],
            in_service: ['completed'],
            completed: [],
            cancelled: [],
        };
        const currencyFormatter = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        let selectedRow = null;
        let availabilityRequest = 0;

        function setMethod(form, method) {
            let methodInput = form.querySelector('input[name="_method"]');

            if (!method) {
                methodInput?.remove();

                return;
            }

            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }

            methodInput.value = method;
        }

        function setCustomerMode(isNewCustomer) {
            const customerSelect = document.getElementById('appointment-customer');
            const newCustomerName = document.getElementById('new-customer-name');
            const newCustomerContact = document.getElementById('new-customer-contact');
            document.getElementById('existing-customer-field').classList.toggle('hidden', isNewCustomer);
            document.getElementById('new-customer-fields').classList.toggle('hidden', !isNewCustomer);
            customerSelect.disabled = isNewCustomer;
            customerSelect.required = !isNewCustomer;
            newCustomerName.disabled = !isNewCustomer;
            newCustomerName.required = isNewCustomer;
            newCustomerContact.disabled = !isNewCustomer;
        }

        function updatePricePreview() {
            const serviceOption = document.getElementById('appointment-service').selectedOptions[0];
            const time = document.getElementById('appointment-form-time').value;
            const priceElement = document.getElementById('appointment-price');

            if (!serviceOption?.value || !time) {
                priceElement.classList.add('hidden');

                return;
            }

            const isHappyHour = time >= happyHourStart && time < happyHourEnd;
            const price = isHappyHour ? serviceOption.dataset.happyHourPrice : serviceOption.dataset.regularPrice;
            priceElement.classList.remove('hidden');

            if (price === '') {
                priceElement.textContent = 'This service is not offered at this time.';

                return;
            }

            priceElement.textContent = `${currencyFormatter.format(Number(price))} · ${isHappyHour ? 'Happy Hour rate' : 'Regular rate'}`;
        }

        function setAvailabilityMessage(message, visible = true, isError = false) {
            const element = document.getElementById('therapist-availability-message');
            element.textContent = message;
            element.classList.toggle('hidden', !visible);
            element.classList.toggle('text-red-700', isError);
            element.classList.toggle('text-ink/60', !isError);
        }

        async function refreshAvailableTherapists(preferredTherapistId = therapistSelect.value) {
            const serviceId = document.getElementById('appointment-service').value;
            const date = document.getElementById('appointment-form-date').value;
            const time = document.getElementById('appointment-form-time').value;
            const appointmentId = document.getElementById('appointment-record-id').value;
            const requestId = ++availabilityRequest;

            if (!serviceId || !date || !time) {
                therapistSelect.replaceChildren(new Option('Choose a service, date, and time first', ''));
                therapistSelect.disabled = true;
                setAvailabilityMessage('', false);

                return;
            }

            therapistSelect.replaceChildren(new Option('Finding available therapists...', ''));
            therapistSelect.disabled = true;
            setAvailabilityMessage('Checking therapist schedules...', true);
            const query = new URLSearchParams({
                service_id: serviceId,
                appointment_date: date,
                appointment_time: time,
            });

            if (appointmentId) {
                query.set('appointment_id', appointmentId);
            }

            try {
                const response = await fetch(`${availabilityUrl}?${query.toString()}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(`Unable to check therapist availability (${response.status}).`);
                }

                const therapists = await response.json();

                if (requestId !== availabilityRequest) {
                    return;
                }

                therapistSelect.replaceChildren(new Option(
                    therapists.length ? 'Select an available therapist' : 'No therapists available for this slot',
                    '',
                ));
                therapists.forEach((therapist) => {
                    therapistSelect.add(new Option(therapist.name, therapist.id));
                });
                therapistSelect.disabled = therapists.length === 0;

                if (preferredTherapistId && therapists.some((therapist) => String(therapist.id) === String(preferredTherapistId))) {
                    therapistSelect.value = preferredTherapistId;
                }

                setAvailabilityMessage(
                    therapists.length ? `${therapists.length} available ${therapists.length === 1 ? 'therapist' : 'therapists'}` : 'No therapists available for this slot',
                    true,
                );
            } catch (error) {
                if (requestId !== availabilityRequest) {
                    return;
                }

                const message = error instanceof Error ? error.message : 'Unable to check therapist availability.';
                therapistSelect.replaceChildren(new Option('Availability could not be loaded', ''));
                therapistSelect.disabled = true;
                setAvailabilityMessage(message, true, true);
                console.error(error);
            }
        }

        function setAppointmentModalOpen(isOpen) {
            appointmentModal.classList.toggle('hidden', !isOpen);
            appointmentModal.classList.toggle('flex', isOpen);
            appointmentModal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                document.getElementById('appointment-customer').focus();
            }
        }

        function openAppointmentModal(appointment = null) {
            appointmentForm.reset();
            const appointmentId = appointment?.id ?? '';
            document.getElementById('appointment-record-id').value = appointmentId;
            document.getElementById('appointment-modal-title').textContent = appointmentId ? 'Edit Appointment' : 'New Appointment';
            document.getElementById('appointment-customer').value = appointment?.customer_id ?? '';
            document.getElementById('new-customer-toggle').checked = Boolean(appointment?.new_customer_name);
            document.getElementById('new-customer-name').value = appointment?.new_customer_name ?? '';
            document.getElementById('new-customer-contact').value = appointment?.new_customer_contact ?? '';
            document.getElementById('appointment-service').value = appointment?.service_id ?? '';
            document.getElementById('appointment-form-date').value = appointment?.appointment_date ?? @json($date);
            document.getElementById('appointment-form-time').value = appointment?.appointment_time ?? '';
            setCustomerMode(Boolean(appointment?.new_customer_name));
            appointmentForm.action = appointmentId
                ? appointmentUpdateUrl.replace('__APPOINTMENT__', encodeURIComponent(appointmentId))
                : appointmentStoreUrl;
            setMethod(appointmentForm, appointmentId ? 'PUT' : null);
            document.querySelector('#appointment-form button[type="submit"]').textContent = appointmentId ? 'Save Changes' : 'Save Appointment';
            updatePricePreview();
            setAppointmentModalOpen(true);
            refreshAvailableTherapists(appointment?.therapist_id ?? '');
        }

        function populateStatusOptions(status) {
            const select = document.getElementById('detail-status');
            const nextStatuses = transitions[status] ?? [];
            select.replaceChildren(...nextStatuses.map((nextStatus) => new Option(statusLabels[nextStatus], nextStatus)));
            const hasNextStatuses = nextStatuses.length > 0;
            select.disabled = !hasNextStatuses;
            document.getElementById('detail-status-form').classList.toggle('hidden', !hasNextStatuses);
            document.getElementById('detail-terminal-status').classList.toggle('hidden', hasNextStatuses);
            document.getElementById('detail-terminal-status').textContent = hasNextStatuses ? '' : 'No further status changes are available.';
        }

        async function showAppointmentDetails(row) {
            selectedRow = row;
            appointmentRows.forEach((appointmentRow) => {
                appointmentRow.classList.toggle('bg-gold/10', appointmentRow === row);
            });

            const errorElement = document.getElementById('appointment-detail-error');
            errorElement.classList.add('hidden');
            document.getElementById('appointment-details-empty').classList.add('hidden');
            document.getElementById('appointment-details-content').classList.remove('hidden');
            document.getElementById('detail-customer').textContent = 'Loading...';

            try {
                const response = await fetch(row.dataset.showUrl, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(`Unable to load appointment details (${response.status}).`);
                }

                const appointment = await response.json();
                if (selectedRow !== row) {
                    return;
                }

                document.getElementById('detail-avatar').textContent = appointment.customer_name
                    .split(/\s+/)
                    .map((part) => part[0])
                    .join('')
                    .slice(0, 2)
                    .toUpperCase();
                document.getElementById('detail-customer').textContent = appointment.customer_name;
                document.getElementById('detail-phone').textContent = appointment.contact_number || '-';
                const customerType = document.getElementById('detail-customer-type');
                customerType.textContent = appointment.customer_type;
                customerType.className = `mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${appointment.is_returning_customer ? 'bg-emerald-100 text-emerald-800' : 'bg-gold/20 text-maroon'}`;
                document.getElementById('detail-date').textContent = appointment.date_label;
                document.getElementById('detail-time').textContent = appointment.time_label;
                document.getElementById('detail-end-time').textContent = appointment.end_time;
                document.getElementById('detail-service').textContent = `${appointment.service_name} (${appointment.service_duration} mins)`;
                document.getElementById('detail-therapist').textContent = appointment.therapist_name;
                document.getElementById('detail-price').textContent = currencyFormatter.format(appointment.reserved_price);
                const currentStatus = document.getElementById('detail-current-status');
                currentStatus.textContent = appointment.status_label;
                currentStatus.className = `inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${statusStyles[appointment.status]}`;
                document.getElementById('detail-status-form').action = appointmentStatusUrl.replace('__APPOINTMENT__', encodeURIComponent(appointment.id));
                populateStatusOptions(appointment.status);

                const canEdit = ['pending', 'confirmed'].includes(appointment.status);
                document.getElementById('edit-appointment').classList.toggle('hidden', !canEdit);
                document.getElementById('ready-for-pos').classList.toggle('hidden', appointment.status !== 'arrived');
                const posLink = document.getElementById('detail-pos-link');
                posLink.href = posUrl.replace('__APPOINTMENT__', encodeURIComponent(appointment.id));
                posLink.classList.toggle('hidden', appointment.status !== 'arrived');
                posLink.classList.toggle('inline-flex', appointment.status === 'arrived');
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Unable to load appointment details.';
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                console.error(error);
            }
        }

        document.querySelectorAll('[data-open-appointment-modal]').forEach((button) => {
            button.addEventListener('click', () => openAppointmentModal());
        });
        document.getElementById('appointment-date').addEventListener('change', () => {
            document.getElementById('appointment-date-form').submit();
        });
        document.getElementById('appointment-service').addEventListener('change', () => {
            updatePricePreview();
            refreshAvailableTherapists('');
        });
        document.getElementById('appointment-form-date').addEventListener('change', () => refreshAvailableTherapists(''));
        document.getElementById('appointment-form-time').addEventListener('change', () => {
            updatePricePreview();
            refreshAvailableTherapists('');
        });
        document.getElementById('new-customer-toggle').addEventListener('change', (event) => {
            setCustomerMode(event.target.checked);
        });
        document.getElementById('close-appointment-modal').addEventListener('click', () => setAppointmentModalOpen(false));
        document.getElementById('cancel-appointment').addEventListener('click', () => setAppointmentModalOpen(false));
        appointmentModal.addEventListener('click', (event) => {
            if (event.target === appointmentModal) {
                setAppointmentModalOpen(false);
            }
        });

        appointmentTable?.addEventListener('click', (event) => {
            const row = event.target.closest('[data-appointment-row]');

            if (!row || event.target.closest('a')) {
                return;
            }

            if (event.target.closest('[data-view-appointment]') || !event.target.closest('button')) {
                showAppointmentDetails(row);
            }
        });
        appointmentTable?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.matches('[data-appointment-row]')) {
                showAppointmentDetails(event.target);
            }
        });

        document.getElementById('edit-appointment').addEventListener('click', () => {
            if (selectedRow) {
                openAppointmentModal({
                    id: selectedRow.dataset.appointmentId,
                    customer_id: selectedRow.dataset.customerId,
                    service_id: selectedRow.dataset.serviceId,
                    appointment_date: selectedRow.dataset.date,
                    appointment_time: selectedRow.dataset.time,
                    therapist_id: selectedRow.dataset.therapistId,
                });
            }
        });
        document.getElementById('quick-search').addEventListener('click', () => appointmentSearch.focus());
        document.getElementById('quick-update-status').addEventListener('click', () => {
            const statusSelect = document.getElementById('detail-status');
            statusSelect.focus();
        });
        document.getElementById('print-schedule').addEventListener('click', () => window.print());
        document.querySelector('[data-reminder-appointment]')?.addEventListener('click', (event) => {
            const appointmentId = event.currentTarget.dataset.reminderAppointment;
            const row = appointmentRows.find((appointmentRow) => appointmentRow.dataset.appointmentId === appointmentId);

            if (row) {
                showAppointmentDetails(row);
            }
        });

        @if ($errors->hasAny(['customer_id', 'new_customer_name', 'new_customer_contact', 'service_id', 'therapist_id', 'appointment_date', 'appointment_time']))
            openAppointmentModal({
                id: @json(old('appointment_record_id')),
                customer_id: @json(old('customer_id')),
                new_customer_name: @json(old('new_customer_name')),
                new_customer_contact: @json(old('new_customer_contact')),
                service_id: @json(old('service_id')),
                appointment_date: @json(old('appointment_date', $date)),
                appointment_time: @json(old('appointment_time')),
                therapist_id: @json(old('therapist_id')),
            });
        @elseif (request()->boolean('new'))
            openAppointmentModal();
        @else
            if (appointmentRows.length > 0) {
                showAppointmentDetails(appointmentRows[0]);
            }
        @endif
    </script>
@endsection
