@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="mb-7">
        <p class="text-sm font-medium text-maroon/70">{{ $todayLabel }}</p>
        <h1 class="mt-1 font-display text-5xl leading-tight text-maroon sm:text-6xl">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-ink/65">Here&apos;s today&apos;s overview at Chillax Spa.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 {{ auth()->user()->isAdmin() ? '2xl:grid-cols-6' : '' }}" aria-label="Today's overview">
        @php
            $dashboardCards = [
                ['label' => "Today's Sales", 'value' => '₱'.number_format($todaySales, 2), 'note' => number_format($paidTransactionsToday).' paid transactions', 'icon' => 'peso'],
                ['label' => "Today's Appointments", 'value' => number_format($todayAppointments), 'note' => number_format($completedAppointments).' completed', 'icon' => 'calendar'],
                ['label' => 'Available Therapists', 'value' => $availableTherapists.' of '.$therapistCount.' therapists', 'note' => 'Currently available', 'icon' => 'person'],
                ['label' => 'Pending Appointments', 'value' => number_format($pendingAppointments), 'note' => 'Today and future', 'icon' => 'clock'],
            ];
            if (auth()->user()->isAdmin()) {
                $dashboardCards[] = ['label' => 'Commission Today', 'value' => '₱'.number_format($adminExtras['commission_today'], 2), 'note' => 'Paid transactions', 'icon' => 'person'];
                $dashboardCards[] = ['label' => 'Sales this month', 'value' => '₱'.number_format($adminExtras['sales_this_month'], 2), 'note' => 'Paid transactions', 'icon' => 'peso'];
            }
        @endphp
        @foreach ($dashboardCards as $stat)
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="text-sm text-ink/65">{{ $stat['label'] }}</p><p class="mt-2 break-words text-2xl font-semibold tracking-tight text-ink">{{ $stat['value'] }}</p><p class="mt-1 text-xs text-ink/50">{{ $stat['note'] }}</p></div><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                    @if ($stat['icon'] === 'peso')<span class="text-lg font-semibold">₱</span>
                    @elseif ($stat['icon'] === 'calendar')<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01"/></svg>
                    @elseif ($stat['icon'] === 'person')<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                    @else<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>@endif
                </span></div>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.75fr)]">
        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-maroon/10 px-5 py-4"><div><h2 class="font-display text-3xl text-maroon">Upcoming Appointments</h2><p class="mt-0.5 text-xs text-ink/55">{{ $todayLabel }}</p></div><a href="{{ route('appointments') }}" class="whitespace-nowrap text-sm font-medium text-maroon hover:text-gold">View all <span aria-hidden="true">→</span></a></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[680px] text-left text-sm"><thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60"><tr><th class="px-5 py-3 font-medium">Time</th><th class="px-5 py-3 font-medium">Customer</th><th class="px-5 py-3 font-medium">Service</th><th class="px-5 py-3 font-medium">Therapist</th><th class="px-5 py-3 font-medium">Status</th></tr></thead>
                <tbody class="divide-y divide-maroon/5">
                    @forelse ($upcomingAppointments as $appointment)
                        @php($status = $appointment->appointment_status)
                        <tr class="hover:bg-cream/40"><td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td><td class="whitespace-nowrap px-5 py-4 text-ink">{{ $appointment->customer->customer_name }}</td><td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $appointment->service->service_name }}</td><td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $appointment->therapist->therapist_name }}</td><td class="px-5 py-4">
                            @if ($status === 'confirmed')<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Confirmed</span>
                            @elseif ($status === 'arrived')<span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">Arrived</span>
                            @elseif ($status === 'in_service')<span class="inline-flex rounded-full bg-teal-100 px-2.5 py-1 text-xs font-semibold text-teal-700">In Service</span>
                            @else<span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Pending</span>@endif
                        </td></tr>
                    @empty<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-ink/55">No more appointments today.</td></tr>
                    @endforelse
                </tbody>
            </table></div>
        </article>

        <aside class="space-y-6">
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><h2 class="font-display text-3xl text-maroon">Therapist Availability</h2><div class="mt-4 divide-y divide-maroon/5">
                @forelse ($therapists as $therapist)
                    @php
                        $initials = collect(preg_split('/\s+/', trim($therapist->therapist_name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
                        $currentServiceAppointment = $therapist->appointments->first();
                        $until = $currentServiceAppointment
                            ? \Illuminate\Support\Carbon::parse($currentServiceAppointment->appointment_date.' '.$currentServiceAppointment->appointment_time, 'Asia/Manila')->addMinutes((int) $currentServiceAppointment->service->duration)->format('H:i')
                            : null;
                    @endphp
                    <div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold/20 text-xs font-semibold text-maroon">{{ $initials }}</span><div class="min-w-0"><span class="block truncate text-sm font-medium text-ink">{{ $therapist->therapist_name }}</span>@if($therapist->availability_status === 'in_service' && $until)<span class="text-xs text-ink/55">until {{ $until }}</span>@endif</div></div>
                        @if ($therapist->availability_status === 'available')<span class="inline-flex shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Available</span>
                        @elseif ($therapist->availability_status === 'in_service')<span class="inline-flex shrink-0 rounded-full bg-teal-100 px-2.5 py-1 text-xs font-semibold text-teal-700">In Service</span>
                        @else<span class="inline-flex shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Off Duty</span>@endif
                    </div>
                @empty<p class="py-5 text-sm text-ink/55">No therapists found.</p>
                @endforelse
            </div></article>

            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><h2 class="font-display text-3xl text-maroon">Quick Actions</h2><div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                @if (Route::has('appointments'))<a href="{{ route('appointments', ['new' => 1]) }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span>New Appointment</a>@endif
                @if (Route::has('pos'))<a href="{{ route('pos') }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg></span>Open POS</a>@endif
                @if (Route::has('customers'))<a href="{{ route('customers', ['new' => 1]) }}" class="flex min-h-12 items-center gap-3 rounded-lg border border-maroon/10 bg-cream/50 px-4 py-3 text-sm font-medium text-ink transition hover:border-gold hover:bg-gold/10"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-maroon" aria-hidden="true"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8v6M16 11h6"/></svg></span>Add Customer</a>@endif
            </div></article>
        </aside>
    </section>

    <section class="mt-6 rounded-xl border border-maroon/10 bg-white p-5 shadow-sm" aria-labelledby="weekly-sales-heading"><div class="mb-4 flex items-center justify-between gap-3"><div><h2 id="weekly-sales-heading" class="font-display text-3xl text-maroon">Weekly Sales</h2><p class="mt-0.5 text-xs text-ink/55">Last 7 days, including today</p></div><span class="rounded-full bg-gold/15 px-3 py-1.5 text-xs font-semibold text-maroon">₱{{ number_format($weeklyTotal, 2) }}</span></div>
        @if ($hasWeeklySales)<div class="h-[280px] w-full"><canvas id="weekly-sales-chart" aria-label="Weekly sales chart"></canvas></div>@else<div class="flex h-[220px] items-center justify-center text-sm text-ink/55">No sales in the last 7 days.</div>@endif
    </section>

    @if ($hasWeeklySales)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const weeklyLabels = @json($weeklyLabels);
            const weeklySales = @json($weeklyValues);
            const peso = value => `₱${Number(value).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            new Chart(document.getElementById('weekly-sales-chart'), {
                type: 'bar',
                data: {labels: weeklyLabels, datasets: [{label: 'Sales', data: weeklySales, backgroundColor: '#6B0F1A', borderColor: '#6B0F1A', borderWidth: 1, borderRadius: 6, borderSkipped: false}]},
                options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false}, tooltip: {callbacks: {label: context => `Sales: ${peso(context.parsed.y)}`}}}, scales: {x: {grid: {display: false}, ticks: {color: '#6B6161'}}, y: {beginAtZero: true, ticks: {color: '#6B6161', callback: value => peso(value)}, grid: {color: 'rgba(107, 15, 26, 0.08)'}}}},
            });
        </script>
    @endif
@endsection
