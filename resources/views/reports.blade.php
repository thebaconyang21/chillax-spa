@extends('layouts.app')

@section('title', 'Reports')
@section('breadcrumb', 'Reports')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end report-heading">
        <div><h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Reports</h1><p class="mt-1 text-sm text-ink/65">Review sales performance and therapist commissions.</p></div>
        <div class="flex gap-2 report-actions">
            <a href="{{ route('reports.export', ['period' => $range->period, 'date' => $range->inputDate()]) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gold px-5 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>Export</a>
            <button type="button" onclick="window.print()" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-maroon/15 bg-white px-5 py-3 text-sm font-semibold text-maroon hover:bg-maroon/5">Print</button>
        </div>
    </div>

    <section class="mb-5 flex flex-col justify-between gap-4 rounded-xl border border-maroon/10 bg-white p-3 shadow-sm lg:flex-row lg:items-center report-controls" aria-label="Report controls">
        <nav class="flex flex-wrap gap-1 report-tabs" aria-label="Report period">
            @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $period => $label)
                <a href="{{ route('reports', ['period' => $period, 'date' => $range->selectedDate->format('Y-m-d')]) }}" @if ($range->period === $period) aria-current="page" @endif class="rounded-lg px-4 py-2.5 text-sm font-medium transition {{ $range->period === $period ? 'bg-gold text-maroon shadow-sm' : 'text-ink/65 hover:bg-cream' }}">{{ $label }}</a>
            @endforeach
        </nav>
        <form id="report-date-form" method="GET" action="{{ route('reports') }}" class="flex items-center gap-3 report-filters">
            <input type="hidden" name="period" value="{{ $range->period }}">
            <label id="report-date-label" for="report-date" class="text-sm font-medium text-ink/70">{{ ucfirst($range->period === 'daily' || $range->period === 'weekly' ? 'date' : ($range->period === 'monthly' ? 'month' : 'year')) }}</label>
            <input id="report-date" name="date" type="{{ $range->period === 'monthly' ? 'month' : ($range->period === 'yearly' ? 'number' : 'date') }}" @if($range->period === 'yearly') min="1900" max="2200" @endif value="{{ $range->inputDate() }}" class="h-10 w-40 rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
        </form>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Report summary">
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><p class="text-sm text-ink/65">Total Sales</p><p class="mt-2 text-2xl font-semibold tracking-tight text-ink">₱{{ number_format($summary['sales'], 2) }}</p>
            <p class="mt-2 text-xs {{ $comparison['direction'] === 'up' ? 'text-emerald-700' : 'text-red-600' }}">@if($comparison['percent'] === null) - @elseif($comparison['direction'] === 'up') ↑ {{ number_format(abs($comparison['percent']), 1) }}% @else ↓ {{ number_format(abs($comparison['percent']), 1) }}% @endif <span class="text-ink/50">vs previous period</span></p>
        </article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><p class="text-sm text-ink/65">Total Transactions</p><p class="mt-2 text-2xl font-semibold tracking-tight text-ink">{{ number_format($summary['transactions']) }}</p><p class="mt-2 text-xs text-ink/55">Average sale: ₱{{ number_format($summary['average_sale'], 2) }}</p></article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><p class="text-sm text-ink/65">Total Therapist Commission</p><p class="mt-2 text-2xl font-semibold tracking-tight text-ink">₱{{ number_format($summary['commission'], 2) }}</p></article>
        <article class="rounded-xl border border-gold/40 bg-gold/10 p-5 shadow-sm"><p class="text-sm text-ink/65">Net Income for the Spa</p><p class="mt-2 text-2xl font-semibold tracking-tight text-maroon">₱{{ number_format($summary['net_income'], 2) }}</p></article>
    </section>

    <section class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm" aria-labelledby="sales-chart-heading">
        <div class="mb-4"><h2 id="sales-chart-heading" class="font-display text-3xl text-maroon">Sales Overview</h2><p id="sales-chart-period" class="mt-0.5 text-xs text-ink/55">{{ $range->label }}</p></div>
        @if ($hasSales)<div class="h-[300px] w-full"><canvas id="sales-chart" aria-label="Sales and therapist commission chart"></canvas></div>@else<div class="flex h-[220px] items-center justify-center text-sm text-ink/55">No sales in this period.</div>@endif
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="border-b border-maroon/10 px-5 py-4"><h2 class="font-display text-3xl text-maroon">Top Services</h2></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[520px] text-left text-sm"><thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60"><tr><th class="px-5 py-3 font-medium">Service Name</th><th class="px-5 py-3 text-right font-medium">Times Availed</th><th class="px-5 py-3 text-right font-medium">Total Sales</th></tr></thead>
                <tbody class="divide-y divide-maroon/5">@forelse($topServices as $service)<tr class="hover:bg-cream/40"><td class="px-5 py-4 font-medium text-ink">{{ $service->service_name }}</td><td class="px-5 py-4 text-right text-ink/75">{{ number_format($service->times_availed) }}</td><td class="px-5 py-4 text-right text-ink/75">₱{{ number_format((float) $service->total_sales, 2) }}</td></tr>@empty<tr><td colspan="3" class="px-5 py-8 text-center text-sm text-ink/55">No data for this period.</td></tr>@endforelse</tbody>
            </table></div>
        </article>

        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="border-b border-maroon/10 px-5 py-4"><h2 class="font-display text-3xl text-maroon">Therapist Commission</h2></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[620px] text-left text-sm"><thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60"><tr><th class="px-5 py-3 font-medium">Therapist</th><th class="px-5 py-3 text-right font-medium">Services Done</th><th class="px-5 py-3 text-right font-medium">Sales</th><th class="px-5 py-3 text-right font-medium">Commission</th></tr></thead>
                <tbody class="divide-y divide-maroon/5">@forelse($therapistCommissions as $therapist)<tr class="hover:bg-cream/40"><td class="px-5 py-4 font-medium text-ink">{{ $therapist->therapist_name }}</td><td class="px-5 py-4 text-right text-ink/75">{{ number_format($therapist->services_done) }}</td><td class="px-5 py-4 text-right text-ink/75">₱{{ number_format((float) $therapist->sales, 2) }}</td><td class="px-5 py-4 text-right text-ink/75">₱{{ number_format((float) $therapist->commission, 2) }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-sm text-ink/55">No data for this period.</td></tr>@endforelse</tbody>
            </table></div>
        </article>
    </section>

    <section class="mt-6 rounded-xl border border-maroon/10 bg-white p-5 shadow-sm" aria-labelledby="payment-methods-heading">
        <h2 id="payment-methods-heading" class="mb-4 font-display text-3xl text-maroon">Payment Methods</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach (['cash' => 'Cash', 'ecash' => 'E-cash'] as $key => $label)
                <article class="rounded-lg bg-cream/60 p-4"><div class="flex items-center justify-between"><h3 class="text-sm font-semibold text-ink">{{ $label }}</h3><span class="text-xs text-ink/55">{{ number_format($paymentMethods[$key]['count']) }} transactions</span></div><p class="mt-2 text-xl font-semibold text-maroon">₱{{ number_format($paymentMethods[$key]['total'], 2) }}</p></article>
            @endforeach
        </div>
    </section>

    @if ($hasSales)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const chartLabels = @json($chartLabels);
            const chartSales = @json($chartSales);
            const chartCommissions = @json($chartCommissions);
            const peso = value => `₱${Number(value).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            new Chart(document.getElementById('sales-chart'), {
                type: 'bar',
                data: {labels: chartLabels, datasets: [
                    {type: 'bar', label: 'Sales', data: chartSales, backgroundColor: 'rgba(107, 15, 26, 0.82)', borderColor: '#6B0F1A', borderWidth: 1, borderRadius: 5, order: 2},
                    {type: 'line', label: 'Therapist Commission', data: chartCommissions, borderColor: '#D4A017', backgroundColor: '#D4A017', pointBackgroundColor: '#D4A017', pointRadius: 4, borderWidth: 2, tension: 0.25, order: 1},
                ]},
                options: {responsive: true, maintainAspectRatio: false, interaction: {mode: 'index', intersect: false}, plugins: {tooltip: {callbacks: {label: context => `${context.dataset.label}: ${peso(context.parsed.y)}`}}}, scales: {x: {grid: {display: false}, ticks: {color: '#6B6161'}}, y: {beginAtZero: true, ticks: {color: '#6B6161', callback: value => peso(value)}, grid: {color: 'rgba(107, 15, 26, 0.08)'}}}},
            });
        </script>
    @endif

    <script>
        document.getElementById('report-date').addEventListener('change', () => document.getElementById('report-date-form').submit());
    </script>
    <style>
        @media print {
            #sidebar, #sidebar-overlay, #app-header, .report-heading, .report-controls, .report-actions, .report-tabs, .report-filters { display: none !important; }
            body, main, .md\:pl-72 { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; background: white !important; }
            article, section { break-inside: avoid; box-shadow: none !important; }
        }
    </style>
@endsection
