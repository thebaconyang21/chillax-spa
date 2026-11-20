@extends('layouts.app')

@section('title', 'Therapist & Commission')
@section('breadcrumb', 'Therapist & Commission')

@section('content')
    @php
        $therapists = [
            [
                'id' => 1,
                'name' => 'Therapist A',
                'phone' => '0917 100 0001',
                'availability' => 'Available',
                'completed_services' => 12,
                'sales' => 5400,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '10:00 AM', 'customer' => 'Ana Reyes', 'service' => 'Swedish Massage'],
                    ['date' => 'Nov 21, 2026', 'time' => '1:00 PM', 'customer' => 'Maria Santos', 'service' => 'Foot Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                    ['date' => 'Nov 19, 2026', 'service' => 'Foot Massage', 'sales' => 400],
                    ['date' => 'Nov 18, 2026', 'service' => 'Combo (Swedish + Add-on)', 'sales' => 500],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Therapist B',
                'phone' => '0917 100 0002',
                'availability' => 'In Service',
                'completed_services' => 10,
                'sales' => 4600,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '9:30 AM', 'customer' => 'Maria Santos', 'service' => 'Therapeutic Massage'],
                    ['date' => 'Nov 21, 2026', 'time' => '2:30 PM', 'customer' => 'Liza Cruz', 'service' => 'Swedish Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Therapeutic Massage', 'sales' => 500],
                    ['date' => 'Nov 18, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Therapist C',
                'phone' => '0917 100 0003',
                'availability' => 'Available',
                'completed_services' => 8,
                'sales' => 3200,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '11:30 AM', 'customer' => 'John Dela Cruz', 'service' => 'Foot Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Foot Massage', 'sales' => 400],
                    ['date' => 'Nov 16, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                ],
            ],
            [
                'id' => 4,
                'name' => 'Therapist D',
                'phone' => '0917 100 0004',
                'availability' => 'Off Duty',
                'completed_services' => 6,
                'sales' => 2500,
                'schedule' => [],
                'service_history' => [
                    ['date' => 'Nov 19, 2026', 'service' => 'Therapeutic Massage', 'sales' => 500],
                    ['date' => 'Nov 17, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                ],
            ],
            [
                'id' => 5,
                'name' => 'Therapist E',
                'phone' => '0917 100 0005',
                'availability' => 'Available',
                'completed_services' => 11,
                'sales' => 5000,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '10:30 AM', 'customer' => 'Liza Cruz', 'service' => 'Swedish Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                    ['date' => 'Nov 18, 2026', 'service' => 'Therapeutic Massage', 'sales' => 500],
                ],
            ],
            [
                'id' => 6,
                'name' => 'Therapist F',
                'phone' => '0917 100 0006',
                'availability' => 'Available',
                'completed_services' => 9,
                'sales' => 3900,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '12:00 PM', 'customer' => 'Ana Reyes', 'service' => 'Foot Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 19, 2026', 'service' => 'Combo (Swedish + Add-on)', 'sales' => 500],
                    ['date' => 'Nov 17, 2026', 'service' => 'Foot Massage', 'sales' => 400],
                ],
            ],
            [
                'id' => 7,
                'name' => 'Therapist G',
                'phone' => '0917 100 0007',
                'availability' => 'In Service',
                'completed_services' => 7,
                'sales' => 3000,
                'schedule' => [
                    ['date' => 'Nov 21, 2026', 'time' => '9:00 AM', 'customer' => 'John Dela Cruz', 'service' => 'Therapeutic Massage'],
                ],
                'service_history' => [
                    ['date' => 'Nov 20, 2026', 'service' => 'Therapeutic Massage', 'sales' => 500],
                    ['date' => 'Nov 17, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                ],
            ],
            [
                'id' => 8,
                'name' => 'Therapist H',
                'phone' => '0917 100 0008',
                'availability' => 'Off Duty',
                'completed_services' => 5,
                'sales' => 2100,
                'schedule' => [],
                'service_history' => [
                    ['date' => 'Nov 18, 2026', 'service' => 'Foot Massage', 'sales' => 400],
                    ['date' => 'Nov 16, 2026', 'service' => 'Swedish Massage', 'sales' => 350],
                ],
            ],
        ];
        $availabilityStyles = [
            'Available' => 'bg-emerald-50 text-emerald-700',
            'In Service' => 'bg-sky-50 text-sky-700',
            'Off Duty' => 'bg-ink/10 text-ink/65',
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Therapist &amp; Commission</h1>
            <p class="mt-1 text-sm text-ink/65">Manage therapist availability, services, and weekly earnings.</p>
        </div>
        <button id="add-therapist" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-gold px-4 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add Therapist
        </button>
    </div>

    <section class="mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-4 shadow-sm lg:flex-row lg:items-end lg:justify-between" aria-label="Filter therapists">
        <div class="grid gap-3 sm:grid-cols-2">
            <label>
                <span class="mb-1.5 block text-xs font-medium text-ink/65">Week start</span>
                <input id="week-start" type="date" value="2026-11-16" class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
            </label>
            <label>
                <span class="mb-1.5 block text-xs font-medium text-ink/65">Week end</span>
                <input id="week-end" type="date" value="2026-11-22" class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
            </label>
        </div>
        <label class="relative block w-full lg:max-w-sm">
            <span class="sr-only">Search therapist name</span>
            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input id="therapist-search" type="search" placeholder="Search therapist name..." class="h-10 w-full rounded-lg border border-maroon/15 bg-white pl-10 pr-3 text-sm outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/25">
        </label>
    </section>

    <section class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Therapist summary">
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
            <p class="text-sm text-ink/60">Total Therapists</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p id="total-therapists" class="text-3xl font-semibold text-ink">{{ count($therapists) }}</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8h3M20.5 6.5v3"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">Registered therapists</p>
        </article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
            <p class="text-sm text-ink/60">Available Now</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p id="available-therapists" class="text-3xl font-semibold text-ink">0</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">Ready for an appointment</p>
        </article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <p class="text-sm text-ink/60">Total Commission This Week</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p id="total-commission" class="text-3xl font-semibold text-maroon">₱0</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">60% therapist share of total sales</p>
        </article>
    </section>

    <section class="min-w-0 overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="therapists-heading">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-maroon/10 px-4 py-4 sm:px-5">
            <div>
                <h2 id="therapists-heading" class="text-base font-semibold text-ink">Therapist Directory</h2>
                <p class="mt-1 text-xs text-ink/55">Weekly completed services, sales, and commission</p>
            </div>
            <span id="visible-therapist-count" class="text-xs text-ink/55">{{ count($therapists) }} therapists</span>
        </div>
        <div class="min-w-0 overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 font-semibold">Therapist Name</th>
                        <th scope="col" class="px-4 py-3.5 font-semibold">Contact Number</th>
                        <th scope="col" class="px-4 py-3.5 font-semibold">Availability</th>
                        <th scope="col" class="px-4 py-3.5 text-right font-semibold">Completed Services</th>
                        <th scope="col" class="px-4 py-3.5 text-right font-semibold">Total Sales</th>
                        <th scope="col" class="px-4 py-3.5 text-right font-semibold">Commission</th>
                        <th scope="col" class="px-4 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="therapist-table-body" class="divide-y divide-maroon/5">
                    @foreach ($therapists as $therapist)
                        <tr
                            data-therapist-row
                            data-therapist-id="{{ $therapist['id'] }}"
                            data-therapist-name="{{ $therapist['name'] }}"
                            data-phone="{{ $therapist['phone'] }}"
                            data-availability="{{ \Illuminate\Support\Str::slug($therapist['availability']) }}"
                            data-completed-services="{{ $therapist['completed_services'] }}"
                            data-sales="{{ $therapist['sales'] }}"
                            data-schedule="{{ json_encode($therapist['schedule']) }}"
                            data-service-history="{{ json_encode($therapist['service_history']) }}"
                            class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-4 py-4 font-medium text-ink">{{ $therapist['name'] }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-ink/75">{{ $therapist['phone'] }}</td>
                            <td class="px-4 py-4">
                                <span data-availability-badge class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium {{ $availabilityStyles[$therapist['availability']] }}">
                                    {{ $therapist['availability'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-ink/75">{{ $therapist['completed_services'] }}</td>
                            <td class="px-4 py-4 text-right font-medium text-ink">₱{{ number_format($therapist['sales']) }}</td>
                            <td data-commission class="whitespace-nowrap px-4 py-4 text-right font-semibold text-maroon">—</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-therapist-action="view" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">View</button>
                                    <button type="button" data-therapist-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="empty-therapists" class="hidden">
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-ink/55">No therapists match your search.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div id="therapist-panel-overlay" class="fixed inset-0 z-40 hidden bg-ink/50" aria-hidden="true"></div>
    <aside id="therapist-panel" class="fixed inset-y-0 right-0 z-50 hidden w-full max-w-md translate-x-full overflow-y-auto border-l border-gold/25 bg-cream shadow-2xl transition-transform duration-300" aria-hidden="true" aria-labelledby="therapist-panel-title">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-maroon/10 bg-cream/95 px-5 py-4 backdrop-blur">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-ink/50">Therapist Profile</p>
                <h2 id="therapist-panel-title" class="mt-1 font-display text-3xl text-maroon">Therapist A</h2>
            </div>
            <button id="close-therapist-panel" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close therapist details">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-5 p-5">
            <section class="rounded-xl border border-maroon/10 bg-white p-4">
                <div class="flex items-center gap-3">
                    <span id="panel-initials" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold/20 text-sm font-semibold text-maroon">TA</span>
                    <div class="min-w-0 flex-1">
                        <p id="panel-phone" class="text-sm text-ink/65">0917 100 0001</p>
                        <span id="panel-availability" class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Available</span>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-maroon/10 pt-4">
                    <div>
                        <p class="text-xs text-ink/55">Services Completed</p>
                        <p id="panel-completed-count" class="mt-1 font-semibold text-ink">12</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink/55">Total Commission</p>
                        <p id="panel-commission" class="mt-1 font-semibold text-maroon">₱3,240</p>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white">
                <div class="border-b border-maroon/10 px-4 py-3">
                    <h3 class="text-sm font-semibold text-ink">Schedule</h3>
                    <p class="mt-0.5 text-xs text-ink/55">Upcoming appointments this week</p>
                </div>
                <div id="panel-schedule" class="divide-y divide-maroon/5"></div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white">
                <div class="border-b border-maroon/10 px-4 py-3">
                    <h3 class="text-sm font-semibold text-ink">Completed Services</h3>
                    <p class="mt-0.5 text-xs text-ink/55">Recent service history</p>
                </div>
                <div id="panel-service-history" class="divide-y divide-maroon/5"></div>
            </section>
        </div>
    </aside>

    <div id="therapist-modal" class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-lg rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="therapist-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="therapist-modal-title" class="text-lg font-semibold text-maroon">Add Therapist</h2>
                    <p class="mt-1 text-xs text-ink/55">Enter the therapist&apos;s contact and availability details.</p>
                </div>
                <button id="close-therapist-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="therapist-form" class="space-y-4 p-5 sm:p-6">
                <input id="therapist-id" type="hidden">
                <div>
                    <label for="therapist-name" class="mb-1.5 block text-sm font-medium text-ink">Name</label>
                    <input id="therapist-name" name="name" type="text" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="therapist-phone" class="mb-1.5 block text-sm font-medium text-ink">Contact Number</label>
                    <input id="therapist-phone" name="phone" type="tel" required placeholder="e.g. 0917 123 4567" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="therapist-availability" class="mb-1.5 block text-sm font-medium text-ink">Availability Status</label>
                    <select id="therapist-availability" name="availability" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        <option value="available">Available</option>
                        <option value="in-service">In Service</option>
                        <option value="off-duty">Off Duty</option>
                    </select>
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button id="cancel-therapist" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Therapist</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const THERAPIST_SHARE = 0.60;
        const currencyFormatter = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
        const therapistTableBody = document.getElementById('therapist-table-body');
        const therapistSearch = document.getElementById('therapist-search');
        const therapistPanel = document.getElementById('therapist-panel');
        const therapistPanelOverlay = document.getElementById('therapist-panel-overlay');
        const therapistModal = document.getElementById('therapist-modal');
        const therapistForm = document.getElementById('therapist-form');
        let nextTherapistId = Math.max(...[...document.querySelectorAll('[data-therapist-row]')].map((row) => Number(row.dataset.therapistId))) + 1;
        let modalTrigger = null;

        const availabilityStyles = {
            available: 'bg-emerald-50 text-emerald-700',
            'in-service': 'bg-sky-50 text-sky-700',
            'off-duty': 'bg-ink/10 text-ink/65',
        };
        const availabilityLabels = {
            available: 'Available',
            'in-service': 'In Service',
            'off-duty': 'Off Duty',
        };

        function formatCurrency(amount) {
            return currencyFormatter.format(amount);
        }

        function calculateCommission(sales) {
            return Number(sales) * THERAPIST_SHARE;
        }

        function updateTherapistMetrics() {
            const rows = [...therapistTableBody.querySelectorAll('[data-therapist-row]')];
            const availableCount = rows.filter((row) => row.dataset.availability === 'available').length;
            const totalCommission = rows.reduce((total, row) => total + calculateCommission(row.dataset.sales), 0);

            document.getElementById('total-therapists').textContent = rows.length;
            document.getElementById('available-therapists').textContent = availableCount;
            document.getElementById('total-commission').textContent = formatCurrency(totalCommission);

            rows.forEach((row) => {
                row.querySelector('[data-commission]').textContent = formatCurrency(calculateCommission(row.dataset.sales));
            });
        }

        function updateTherapistFilter() {
            const searchTerm = therapistSearch.value.trim().toLowerCase();
            let visibleCount = 0;

            therapistTableBody.querySelectorAll('[data-therapist-row]').forEach((row) => {
                const matches = row.dataset.therapistName.toLowerCase().includes(searchTerm);
                row.classList.toggle('hidden', !matches);
                visibleCount += matches ? 1 : 0;
            });

            document.getElementById('empty-therapists').classList.toggle('hidden', visibleCount > 0);
            document.getElementById('visible-therapist-count').textContent = `${visibleCount} ${visibleCount === 1 ? 'therapist' : 'therapists'}`;
        }

        function createTherapistRow(therapist) {
            const row = document.createElement('tr');
            row.dataset.therapistRow = '';
            row.dataset.therapistId = therapist.id;
            row.dataset.therapistName = therapist.name;
            row.dataset.phone = therapist.phone;
            row.dataset.availability = therapist.availability;
            row.dataset.completedServices = therapist.completedServices;
            row.dataset.sales = therapist.sales;
            row.dataset.schedule = JSON.stringify(therapist.schedule);
            row.dataset.serviceHistory = JSON.stringify(therapist.serviceHistory);
            row.className = 'transition hover:bg-cream/45';

            const nameCell = document.createElement('td');
            nameCell.className = 'whitespace-nowrap px-4 py-4 font-medium text-ink';
            nameCell.textContent = therapist.name;
            row.appendChild(nameCell);

            const phoneCell = document.createElement('td');
            phoneCell.className = 'whitespace-nowrap px-4 py-4 text-ink/75';
            phoneCell.textContent = therapist.phone;
            row.appendChild(phoneCell);

            const availabilityCell = document.createElement('td');
            availabilityCell.className = 'px-4 py-4';
            const badge = document.createElement('span');
            badge.dataset.availabilityBadge = '';
            badge.className = `inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ${availabilityStyles[therapist.availability]}`;
            badge.textContent = availabilityLabels[therapist.availability];
            availabilityCell.appendChild(badge);
            row.appendChild(availabilityCell);

            const completedCell = document.createElement('td');
            completedCell.className = 'px-4 py-4 text-right text-ink/75';
            completedCell.textContent = therapist.completedServices;
            row.appendChild(completedCell);

            const salesCell = document.createElement('td');
            salesCell.className = 'px-4 py-4 text-right font-medium text-ink';
            salesCell.textContent = formatCurrency(therapist.sales);
            row.appendChild(salesCell);

            const commissionCell = document.createElement('td');
            commissionCell.dataset.commission = '';
            commissionCell.className = 'whitespace-nowrap px-4 py-4 text-right font-semibold text-maroon';
            row.appendChild(commissionCell);

            const actionsCell = document.createElement('td');
            actionsCell.className = 'px-4 py-4';
            const actions = document.createElement('div');
            actions.className = 'flex justify-end gap-2';

            [['view', 'View', 'text-maroon'], ['edit', 'Edit', 'text-ink']].forEach(([action, label, color]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.therapistAction = action;
                button.className = `rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium transition hover:border-gold hover:bg-gold/10 ${color}`;
                button.textContent = label;
                actions.appendChild(button);
            });

            actionsCell.appendChild(actions);
            row.appendChild(actionsCell);

            return row;
        }

        function renderHistoryList(container, entries, type) {
            container.replaceChildren();

            if (entries.length === 0) {
                const emptyMessage = document.createElement('p');
                emptyMessage.className = 'p-4 text-sm text-ink/55';
                emptyMessage.textContent = type === 'schedule' ? 'No upcoming appointments.' : 'No completed services yet.';
                container.appendChild(emptyMessage);

                return;
            }

            entries.forEach((entry) => {
                const item = document.createElement('article');
                item.className = 'flex items-start gap-3 p-4';
                const marker = document.createElement('span');
                marker.className = 'mt-1 h-2 w-2 shrink-0 rounded-full bg-gold';
                marker.setAttribute('aria-hidden', 'true');

                const content = document.createElement('div');
                content.className = 'min-w-0 flex-1';
                const heading = document.createElement('div');
                heading.className = 'flex flex-wrap items-start justify-between gap-2';
                const description = document.createElement('div');
                description.className = 'min-w-0';
                const title = document.createElement('h4');
                title.className = 'text-sm font-medium text-ink';
                title.textContent = type === 'schedule' ? entry.customer : entry.service;
                const subtitle = document.createElement('p');
                subtitle.className = 'mt-1 text-xs text-ink/55';
                subtitle.textContent = type === 'schedule' ? entry.service : entry.date;
                description.append(title, subtitle);

                const timeOrPrice = document.createElement('span');
                timeOrPrice.className = 'whitespace-nowrap text-xs font-medium text-maroon';
                timeOrPrice.textContent = type === 'schedule' ? entry.time : formatCurrency(entry.sales);
                heading.append(description, timeOrPrice);

                const detail = document.createElement('p');
                detail.className = 'mt-1 text-xs text-ink/55';
                detail.textContent = type === 'schedule' ? entry.date : 'Completed';
                content.append(heading, detail);
                item.append(marker, content);
                container.appendChild(item);
            });
        }

        function setTherapistPanelOpen(isOpen) {
            therapistPanel.classList.toggle('hidden', !isOpen);
            therapistPanel.classList.toggle('translate-x-full', !isOpen);
            therapistPanelOverlay.classList.toggle('hidden', !isOpen);
            therapistPanel.setAttribute('aria-hidden', String(!isOpen));
            therapistPanelOverlay.setAttribute('aria-hidden', String(!isOpen));
        }

        function showTherapistDetails(row) {
            const sales = Number(row.dataset.sales);
            const name = row.dataset.therapistName;
            const initials = name.split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase();
            const availability = row.dataset.availability;
            const availabilityBadge = document.getElementById('panel-availability');

            document.getElementById('therapist-panel-title').textContent = name;
            document.getElementById('panel-initials').textContent = initials;
            document.getElementById('panel-phone').textContent = row.dataset.phone;
            document.getElementById('panel-completed-count').textContent = row.dataset.completedServices;
            document.getElementById('panel-commission').textContent = formatCurrency(calculateCommission(sales));
            availabilityBadge.className = `mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${availabilityStyles[availability]}`;
            availabilityBadge.textContent = availabilityLabels[availability];
            renderHistoryList(document.getElementById('panel-schedule'), JSON.parse(row.dataset.schedule), 'schedule');
            renderHistoryList(document.getElementById('panel-service-history'), JSON.parse(row.dataset.serviceHistory), 'history');
            setTherapistPanelOpen(true);
        }

        function setTherapistModalOpen(isOpen) {
            therapistModal.classList.toggle('hidden', !isOpen);
            therapistModal.classList.toggle('flex', isOpen);
            therapistModal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                document.getElementById('therapist-name').focus();
            } else if (modalTrigger) {
                modalTrigger.focus();
            }
        }

        function openTherapistEditor(row = null) {
            modalTrigger = document.activeElement;
            therapistForm.reset();
            document.getElementById('therapist-id').value = row?.dataset.therapistId ?? '';
            document.getElementById('therapist-modal-title').textContent = row ? 'Edit Therapist' : 'Add Therapist';
            document.getElementById('therapist-name').value = row?.dataset.therapistName ?? '';
            document.getElementById('therapist-phone').value = row?.dataset.phone ?? '';
            document.getElementById('therapist-availability').value = row?.dataset.availability ?? 'available';
            setTherapistModalOpen(true);
        }

        document.getElementById('add-therapist').addEventListener('click', () => openTherapistEditor());
        document.getElementById('close-therapist-modal').addEventListener('click', () => setTherapistModalOpen(false));
        document.getElementById('cancel-therapist').addEventListener('click', () => setTherapistModalOpen(false));
        document.getElementById('close-therapist-panel').addEventListener('click', () => setTherapistPanelOpen(false));
        therapistPanelOverlay.addEventListener('click', () => setTherapistPanelOpen(false));
        therapistSearch.addEventListener('input', updateTherapistFilter);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (!therapistModal.classList.contains('hidden')) {
                    setTherapistModalOpen(false);
                } else if (!therapistPanel.classList.contains('hidden')) {
                    setTherapistPanelOpen(false);
                }
            }
        });
        therapistModal.addEventListener('click', (event) => {
            if (event.target === therapistModal) {
                setTherapistModalOpen(false);
            }
        });

        therapistTableBody.addEventListener('click', (event) => {
            const actionButton = event.target.closest('[data-therapist-action]');

            if (!actionButton) {
                return;
            }

            const row = actionButton.closest('[data-therapist-row]');

            if (actionButton.dataset.therapistAction === 'view') {
                showTherapistDetails(row);

                return;
            }

            openTherapistEditor(row);
        });

        therapistForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const therapistId = document.getElementById('therapist-id').value;
            const currentRow = therapistId
                ? therapistTableBody.querySelector(`[data-therapist-id="${therapistId}"]`)
                : null;
            const therapist = {
                id: therapistId || nextTherapistId++,
                name: document.getElementById('therapist-name').value.trim(),
                phone: document.getElementById('therapist-phone').value.trim(),
                availability: document.getElementById('therapist-availability').value,
                completedServices: currentRow?.dataset.completedServices ?? 0,
                sales: currentRow?.dataset.sales ?? 0,
                schedule: currentRow ? JSON.parse(currentRow.dataset.schedule) : [],
                serviceHistory: currentRow ? JSON.parse(currentRow.dataset.serviceHistory) : [],
            };
            const newRow = createTherapistRow(therapist);

            if (currentRow) {
                currentRow.replaceWith(newRow);
            } else {
                therapistTableBody.appendChild(newRow);
            }

            updateTherapistMetrics();
            updateTherapistFilter();
            setTherapistModalOpen(false);

            if (!currentRow) {
                showTherapistDetails(newRow);
            }
        });

        updateTherapistMetrics();
        updateTherapistFilter();
    </script>
@endsection
