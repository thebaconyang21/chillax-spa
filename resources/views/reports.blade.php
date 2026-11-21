@extends('layouts.app')

@section('title', 'Reports')
@section('breadcrumb', 'Reports')

@section('content')
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Reports</h1>
            <p class="mt-1 text-sm text-ink/65">Review sales performance and therapist commissions.</p>
        </div>
        <button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-gold px-5 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
            Export
        </button>
    </div>

    <section class="mb-5 flex flex-col justify-between gap-4 rounded-xl border border-maroon/10 bg-white p-3 shadow-sm lg:flex-row lg:items-center" aria-label="Report controls">
        <div class="flex flex-wrap gap-1" role="tablist" aria-label="Report period">
            @foreach (['Daily', 'Weekly', 'Monthly', 'Yearly'] as $tab)
                <button type="button" role="tab" aria-selected="{{ $tab === 'Daily' ? 'true' : 'false' }}" data-report-tab="{{ $tab }}" class="report-tab rounded-lg px-4 py-2.5 text-sm font-medium transition {{ $tab === 'Daily' ? 'bg-maroon text-white shadow-sm' : 'text-ink/65 hover:bg-cream' }}">
                    {{ $tab }}
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-3">
            <label id="report-date-label" for="report-date" class="text-sm font-medium text-ink/70">Date</label>
            <input id="report-date" type="date" value="2026-11-21" class="h-10 rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Report summary">
        @foreach ([
            ['label' => 'Total Sales', 'value' => '₱8,450', 'icon' => 'peso', 'accent' => 'bg-gold/15'],
            ['label' => 'Total Transactions', 'value' => '12', 'icon' => 'receipt', 'accent' => 'bg-maroon/10'],
            ['label' => 'Total Therapist Commission', 'value' => '₱1,690', 'icon' => 'person', 'accent' => 'bg-gold/15'],
            ['label' => 'Net Income for the Spa', 'value' => '₱6,760', 'icon' => 'chart', 'accent' => 'bg-maroon/10'],
        ] as $card)
            <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink/65">{{ $card['label'] }}</p>
                        <p id="summary-{{ Str::slug($card['label']) }}" class="mt-2 text-2xl font-semibold tracking-tight text-ink">{{ $card['value'] }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $card['accent'] }} text-maroon">
                        @if ($card['icon'] === 'peso')
                            <span class="text-lg font-semibold">₱</span>
                        @elseif ($card['icon'] === 'receipt')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 3h14v18l-3-2-4 2-4-2-3 2V3Z"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>
                        @elseif ($card['icon'] === 'person')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                        @endif
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-6 rounded-xl border border-maroon/10 bg-white p-5 shadow-sm" aria-labelledby="sales-chart-heading">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 id="sales-chart-heading" class="font-display text-3xl text-maroon">Sales Overview</h2>
                <p id="sales-chart-period" class="mt-0.5 text-xs text-ink/55">Saturday, November 21, 2026</p>
            </div>
        </div>
        <div class="h-[300px] w-full">
            <canvas id="sales-chart" aria-label="Sales chart"></canvas>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="border-b border-maroon/10 px-5 py-4">
                <h2 class="font-display text-3xl text-maroon">Top Services</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Service Name</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Times Availed</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody id="top-services-body" class="divide-y divide-maroon/5"></tbody>
                </table>
            </div>
        </article>

        <article class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm">
            <div class="border-b border-maroon/10 px-5 py-4">
                <h2 class="font-display text-3xl text-maroon">Therapist Commission</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-cream/80 text-xs uppercase tracking-wide text-ink/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Therapist</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Services Done</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Sales</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Commission</th>
                        </tr>
                    </thead>
                    <tbody id="therapist-commission-body" class="divide-y divide-maroon/5"></tbody>
                </table>
            </div>
        </article>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const reportData = {
            Daily: {
                summary: ['₱8,450', '12', '₱1,690', '₱6,760'],
                labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'],
                sales: [450, 980, 1450, 1280, 1680, 1920, 690],
                period: 'Saturday, November 21, 2026',
                dateType: 'date',
                dateLabel: 'Date',
                dateValue: '2026-11-21',
                services: [
                    ['Swedish Massage', 5, '₱3,250'],
                    ['Therapeutic Massage', 3, '₱2,400'],
                    ['Foot Massage', 3, '₱1,800'],
                    ['Combo', 1, '₱1,000'],
                ],
                therapists: [
                    ['Ana Reyes', 4, '₱3,100', '₱620'],
                    ['Liza Cruz', 3, '₱2,050', '₱410'],
                    ['Maya Flores', 3, '₱1,950', '₱390'],
                    ['Nina Santos', 2, '₱1,350', '₱270'],
                ],
            },
            Weekly: {
                summary: ['₱54,680', '78', '₱10,936', '₱43,744'],
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                sales: [6250, 7100, 6800, 7750, 8430, 8450, 8900],
                period: 'November 16–22, 2026',
                dateType: 'week',
                dateLabel: 'Week',
                dateValue: '2026-W47',
                services: [
                    ['Swedish Massage', 29, '₱19,450'],
                    ['Therapeutic Massage', 18, '₱14,600'],
                    ['Foot Massage', 17, '₱10,200'],
                    ['Combo', 14, '₱10,430'],
                ],
                therapists: [
                    ['Ana Reyes', 21, '₱15,280', '₱3,056'],
                    ['Liza Cruz', 19, '₱13,450', '₱2,690'],
                    ['Maya Flores', 20, '₱13,800', '₱2,760'],
                    ['Nina Santos', 18, '₱12,150', '₱2,430'],
                ],
            },
            Monthly: {
                summary: ['₱224,350', '318', '₱44,870', '₱179,480'],
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                sales: [42600, 48800, 45550, 52350, 35050],
                period: 'November 2026',
                dateType: 'month',
                dateLabel: 'Month',
                dateValue: '2026-11',
                services: [
                    ['Swedish Massage', 112, '₱76,320'],
                    ['Therapeutic Massage', 76, '₱61,400'],
                    ['Foot Massage', 70, '₱42,000'],
                    ['Combo', 60, '₱44,630'],
                ],
                therapists: [
                    ['Ana Reyes', 84, '₱61,350', '₱12,270'],
                    ['Liza Cruz', 79, '₱54,800', '₱10,960'],
                    ['Maya Flores', 80, '₱56,200', '₱11,240'],
                    ['Nina Santos', 75, '₱52,000', '₱10,400'],
                ],
            },
            Yearly: {
                summary: ['₱2,468,500', '3,540', '₱493,700', '₱1,974,800'],
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                sales: [186000, 192500, 205000, 198000, 214500, 220800, 225000, 218500, 235000, 228500, 224350, 120350],
                period: 'January–December 2026',
                dateType: 'number',
                dateLabel: 'Year',
                dateValue: '2026',
                services: [
                    ['Swedish Massage', 1186, '₱820,500'],
                    ['Therapeutic Massage', 740, '₱704,200'],
                    ['Foot Massage', 710, '₱426,000'],
                    ['Combo', 654, '₱517,800'],
                ],
                therapists: [
                    ['Ana Reyes', 920, '₱682,400', '₱136,480'],
                    ['Liza Cruz', 884, '₱615,200', '₱123,040'],
                    ['Maya Flores', 890, '₱601,900', '₱120,380'],
                    ['Nina Santos', 846, '₱569,000', '₱113,800'],
                ],
            },
        };

        let salesChart;
        const chartCanvas = document.getElementById('sales-chart');
        const dateInput = document.getElementById('report-date');
        const dateLabel = document.getElementById('report-date-label');
        const summaryIds = [
            'summary-total-sales',
            'summary-total-transactions',
            'summary-total-therapist-commission',
            'summary-net-income-for-the-spa',
        ];
        const peso = (value) => `₱${Number(value).toLocaleString('en-PH')}`;

        function renderRows(tableId, rows, nameIndex) {
            const tableBody = document.getElementById(tableId);
            tableBody.innerHTML = rows.map((row) => `
                <tr class="hover:bg-cream/40">
                    ${row.map((cell, index) => `
                        <td class="px-5 py-4 ${index === nameIndex ? 'font-medium text-ink' : 'text-right text-ink/75'}">${cell}</td>
                    `).join('')}
                </tr>
            `).join('');
        }

        function selectReportTab(tabName) {
            const data = reportData[tabName];

            document.querySelectorAll('[data-report-tab]').forEach((tab) => {
                const isSelected = tab.dataset.reportTab === tabName;
                tab.setAttribute('aria-selected', String(isSelected));
                tab.classList.toggle('bg-maroon', isSelected);
                tab.classList.toggle('text-white', isSelected);
                tab.classList.toggle('shadow-sm', isSelected);
                tab.classList.toggle('text-ink/65', !isSelected);
                tab.classList.toggle('hover:bg-cream', !isSelected);
            });

            dateInput.type = data.dateType;
            dateInput.value = data.dateValue;
            if (data.dateType === 'number') dateInput.min = '2020';
            if (data.dateType === 'number') dateInput.max = '2035';
            dateLabel.textContent = data.dateLabel;
            data.summary.forEach((value, index) => {
                document.getElementById(summaryIds[index]).textContent = value;
            });
            document.getElementById('sales-chart-period').textContent = data.period;

            renderRows('top-services-body', data.services, 0);
            renderRows('therapist-commission-body', data.therapists, 0);

            const chartData = {
                labels: data.labels,
                datasets: [{
                    label: 'Sales',
                    data: data.sales,
                    borderColor: '#721422',
                    backgroundColor: 'rgba(201, 166, 70, 0.28)',
                    pointBackgroundColor: '#C9A646',
                    pointBorderColor: '#721422',
                    pointRadius: 4,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                }],
            };

            if (salesChart) {
                salesChart.data = chartData;
                salesChart.update();
            } else {
                salesChart = new Chart(chartCanvas, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `Sales: ${peso(context.parsed.y)}`,
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
            }
        }

        document.querySelectorAll('[data-report-tab]').forEach((tab) => {
            tab.addEventListener('click', () => selectReportTab(tab.dataset.reportTab));
        });

        selectReportTab('Daily');
    </script>
@endsection
