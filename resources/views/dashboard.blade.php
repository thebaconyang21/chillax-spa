@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $appointments = [
            ['time' => '11:30 AM', 'customer' => 'Maria Santos', 'service' => 'Swedish Massage', 'therapist' => 'Ana Reyes', 'status' => 'Confirmed'],
            ['time' => '12:30 PM', 'customer' => 'John Rivera', 'service' => 'Foot Massage', 'therapist' => 'Liza Cruz', 'status' => 'Arrived'],
            ['time' => '2:00 PM', 'customer' => 'Angela Cruz', 'service' => 'Therapeutic Massage', 'therapist' => 'Maya Flores', 'status' => 'Pending'],
            ['time' => '3:30 PM', 'customer' => 'Sophia Mendoza', 'service' => 'Combo', 'therapist' => 'Nina Santos', 'status' => 'Confirmed'],
            ['time' => '5:00 PM', 'customer' => 'Carlos Garcia', 'service' => 'Hot Stone Massage', 'therapist' => 'Ana Reyes', 'status' => 'Pending'],
        ];

        $therapists = [
            ['name' => 'Ana Reyes', 'status' => 'Available'],
            ['name' => 'Liza Cruz', 'status' => 'In Service'],
            ['name' => 'Maya Flores', 'status' => 'Available'],
            ['name' => 'Nina Santos', 'status' => 'Off Duty'],
        ];
    @endphp

    <div class="mb-7">
        <p class="text-sm font-medium text-maroon/70">Saturday, November 21, 2026</p>
        <h1 class="mt-1 font-display text-5xl leading-tight text-maroon sm:text-6xl">Welcome back, Jane Dela Cruz</h1>
        <p class="mt-1 text-sm text-ink/65">Here&apos;s today&apos;s overview at Chillax Spa.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Today's overview">
        @foreach ([
            ['label' => "Today's Sales", 'value' => '₱8,450', 'note' => '12 transactions', 'icon' => 'peso'],
            ['label' => "Today's Appointments", 'value' => '18', 'note' => '6 still upcoming', 'icon' => 'calendar'],
            ['label' => 'Available Therapists', 'value' => '3', 'note' => 'Out of 4 on shift', 'icon' => 'person'],
            ['label' => 'Pending Appointments', 'value' => '5', 'note' => 'Require confirmation', 'icon' => 'clock'],
        ] as $stat)
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink/65">{{ $stat['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold tracking-tight text-ink">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs text-ink/50">{{ $stat['note'] }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                        @if ($stat['icon'] === 'peso')
                            <span class="text-lg font-semibold">₱</span>
                        @elseif ($stat['icon'] === 'calendar')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01"/></svg>
                        @elseif ($stat['icon'] === 'person')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        @endif
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.75fr)]">
        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-maroon/10 px-5 py-4">
                <div>
                    <h2 class="font-display text-3xl text-maroon">Upcoming Appointments</h2>
                    <p class="mt-0.5 text-xs text-ink/55">Saturday, November 21, 2026</p>
                </div>
                <a href="{{ route('appointments') }}" class="text-sm font-medium text-maroon hover:text-gold">View all <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-left text-sm">
                    <thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Time</th>
                            <th scope="col" class="px-5 py-3 font-medium">Customer</th>
                            <th scope="col" class="px-5 py-3 font-medium">Service</th>
                            <th scope="col" class="px-5 py-3 font-medium">Therapist</th>
                            <th scope="col" class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-maroon/5">
                        @foreach ($appointments as $appointment)
                            <tr class="hover:bg-cream/40">
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ $appointment['time'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-ink">{{ $appointment['customer'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $appointment['service'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $appointment['therapist'] }}</td>
                                <td class="px-5 py-4">
                                    @if ($appointment['status'] === 'Confirmed')
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Confirmed</span>
                                    @elseif ($appointment['status'] === 'Arrived')
                                        <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">Arrived</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="space-y-6">
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <h2 class="font-display text-3xl text-maroon">Therapist Availability</h2>
                <div class="mt-4 divide-y divide-maroon/5">
                    @foreach ($therapists as $therapist)
                        <div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gold/20 text-xs font-semibold text-maroon">
                                    {{ collect(explode(' ', $therapist['name']))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                                </span>
                                <span class="text-sm font-medium text-ink">{{ $therapist['name'] }}</span>
                            </div>
                            @if ($therapist['status'] === 'Available')
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Available</span>
                            @elseif ($therapist['status'] === 'In Service')
                                <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">In Service</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Off Duty</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <h2 class="font-display text-3xl text-maroon">Quick Actions</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <a href="{{ route('appointments') }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                        New Appointment
                    </a>
                    <a href="{{ route('pos') }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                        </span>
                        Open POS
                    </a>
                    <a href="{{ route('customers') }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8v6M16 11h6"/></svg>
                        </span>
                        Add Customer
                    </a>
                </div>
            </article>
        </aside>
    </section>

    <section class="mt-6 rounded-xl border border-maroon/10 bg-white p-5 shadow-sm" aria-labelledby="weekly-sales-heading">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 id="weekly-sales-heading" class="font-display text-3xl text-maroon">Weekly Sales</h2>
                <p class="mt-0.5 text-xs text-ink/55">November 16–22, 2026</p>
            </div>
            <span class="rounded-full bg-gold/15 px-3 py-1.5 text-xs font-semibold text-maroon">₱54,680</span>
        </div>
        <div class="h-[280px] w-full">
            <canvas id="weekly-sales-chart" aria-label="Weekly sales chart"></canvas>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('weekly-sales-chart'), {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales',
                    data: [6250, 7100, 6800, 7750, 8430, 8450, 8900],
                    backgroundColor: ['rgba(114,20,34,0.88)', 'rgba(114,20,34,0.88)', 'rgba(114,20,34,0.88)', 'rgba(114,20,34,0.88)', 'rgba(114,20,34,0.88)', '#C9A646', '#C9A646'],
                    borderColor: '#721422',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `Sales: ₱${Number(context.parsed.y).toLocaleString('en-PH')}`,
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6B6161' } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6B6161',
                            callback: (value) => `₱${Number(value).toLocaleString('en-PH')}`,
                        },
                        grid: { color: 'rgba(114, 20, 34, 0.08)' },
                    },
                },
            },
        });
    </script>
@endsection
