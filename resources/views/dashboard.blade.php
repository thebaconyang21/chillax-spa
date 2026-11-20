@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-maroon/70">Saturday, November 21, 2026</p>
            <h1 class="mt-1 font-display text-5xl leading-tight text-maroon sm:text-6xl">Welcome back, Jane</h1>
            <p class="mt-1 text-sm text-ink/65">Here&apos;s what&apos;s happening at Chillax Spa today.</p>
        </div>
        <a href="#" class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-gold px-5 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            New Appointment
        </a>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Today's overview">
        @foreach ([
            ['label' => "Today's Appointments", 'value' => '18', 'note' => '6 still to come', 'icon' => 'calendar'],
            ['label' => 'Customers Served', 'value' => '12', 'note' => '2 currently in service', 'icon' => 'users'],
            ['label' => "Today's Revenue", 'value' => '₱ 8,450', 'note' => 'Across 12 transactions', 'icon' => 'chart'],
            ['label' => 'Available Therapists', 'value' => '5', 'note' => 'Out of 8 on shift', 'icon' => 'person'],
        ] as $stat)
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink/65">{{ $stat['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold tracking-tight text-ink">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs text-ink/50">{{ $stat['note'] }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @switch($stat['icon'])
                                @case('calendar')
                                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01"/>
                                    @break
                                @case('users')
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M23 11h-6"/>
                                    @break
                                @case('chart')
                                    <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
                                    @break
                                @case('person')
                                    <circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/>
                                    @break
                            @endswitch
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-7 grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,0.8fr)]">
        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-maroon/10 px-5 py-4">
                <div>
                    <h2 class="font-display text-3xl text-maroon">Today&apos;s Appointments</h2>
                    <p class="mt-0.5 text-xs text-ink/55">Saturday, November 21, 2026</p>
                </div>
                <a href="#" class="text-sm font-medium text-maroon hover:text-gold">View all <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60">
                        <tr>
                            <th class="px-5 py-3 font-medium">Time</th>
                            <th class="px-5 py-3 font-medium">Customer</th>
                            <th class="px-5 py-3 font-medium">Service</th>
                            <th class="px-5 py-3 font-medium">Therapist</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-maroon/5">
                        @foreach ([
                            ['time' => '11:00 AM', 'customer' => 'Ana Reyes', 'service' => 'Swedish Massage', 'therapist' => 'Therapist A', 'status' => 'Completed', 'style' => 'bg-emerald-50 text-emerald-700'],
                            ['time' => '1:00 PM', 'customer' => 'Maria Santos', 'service' => 'Swedish Massage', 'therapist' => 'Therapist B', 'status' => 'Arrived', 'style' => 'bg-sky-50 text-sky-700'],
                            ['time' => '3:30 PM', 'customer' => 'Liza Cruz', 'service' => 'Foot Massage', 'therapist' => 'Therapist A', 'status' => 'Confirmed', 'style' => 'bg-violet-50 text-violet-700'],
                            ['time' => '5:00 PM', 'customer' => 'John Dela Cruz', 'service' => 'Combo Massage', 'therapist' => 'Therapist C', 'status' => 'Confirmed', 'style' => 'bg-violet-50 text-violet-700'],
                            ['time' => '7:00 PM', 'customer' => 'Anna Garcia', 'service' => 'Therapeutic Massage', 'therapist' => 'Therapist B', 'status' => 'Pending', 'style' => 'bg-amber-50 text-amber-700'],
                        ] as $appointment)
                            <tr class="hover:bg-cream/40">
                                <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $appointment['time'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $appointment['customer'] }}</td>
                                <td class="px-5 py-4">{{ $appointment['service'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $appointment['therapist'] }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $appointment['style'] }}">{{ $appointment['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="space-y-6">
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <h2 class="font-display text-3xl text-maroon">Quick Actions</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    @foreach ([['icon' => '+', 'label' => 'New Appointment'], ['icon' => '⌕', 'label' => 'Find Customer'], ['icon' => '◷', 'label' => 'Point of Sale'], ['icon' => '▤', 'label' => 'View Reports']] as $action)
                        <a href="#" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-lg border border-maroon/10 bg-cream/50 px-3 py-4 text-center text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">
                            <span class="text-2xl leading-none text-maroon" aria-hidden="true">{{ $action['icon'] }}</span>
                            {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="rounded-xl border border-gold/40 bg-gold/10 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-3xl text-maroon">Upcoming Reminder</h2>
                    <span class="text-xs font-medium text-maroon/70">In 30 min</span>
                </div>
                <div class="mt-4 flex gap-3 rounded-lg bg-white/80 p-4">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <div>
                        <p class="text-sm font-semibold">3:30 PM &middot; Liza Cruz</p>
                        <p class="mt-1 text-xs leading-relaxed text-ink/65">Swedish Massage &middot; Therapist A</p>
                    </div>
                </div>
            </article>
        </aside>
    </section>
@endsection
