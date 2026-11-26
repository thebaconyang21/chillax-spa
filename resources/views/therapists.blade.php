@extends('layouts.app')

@section('title', 'Therapist & Commission')
@section('breadcrumb', 'Therapist & Commission')

@section('content')
    @php
        $availabilityLabels = [
            'available' => 'Available',
            'in_service' => 'In Service',
            'off_duty' => 'Off Duty',
        ];
        $availabilityStyles = [
            'available' => 'bg-emerald-50 text-emerald-700',
            'in_service' => 'bg-sky-50 text-sky-700',
            'off_duty' => 'bg-ink/10 text-ink/65',
        ];
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

    <form method="GET" action="{{ route('therapists') }}" class="mb-5 flex flex-col gap-3 rounded-xl border border-maroon/10 bg-white p-4 sm:flex-row sm:items-end sm:justify-between" aria-label="Filter therapists">
        <div class="grid gap-3 sm:grid-cols-2">
            <label>
                <span class="mb-1.5 block text-xs font-medium text-ink/65">Week start</span>
                <input name="week_start" type="date" value="{{ old('week_start', $weekStart->toDateString()) }}" class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
            </label>
            <label class="relative block w-full sm:min-w-64">
                <span class="mb-1.5 block text-xs font-medium text-ink/65">Search therapist</span>
                <svg class="pointer-events-none absolute left-3 top-[calc(50%+0.45rem)] h-5 w-5 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input name="search" type="search" value="{{ $search }}" placeholder="Search therapist name..." class="h-10 w-full rounded-lg border border-maroon/15 bg-white pl-10 pr-3 text-sm text-ink outline-none placeholder:text-ink/45 focus:border-gold focus:ring-2 focus:ring-gold/25">
            </label>
        </div>
        <button type="submit" class="min-h-10 rounded-lg border border-maroon/15 px-4 py-2 text-sm font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Apply Filters</button>
    </form>

    <p class="mb-4 text-sm text-ink/65">
        Selected week:
        <span class="font-semibold text-ink">{{ $weekStart->format('M j') }} - {{ $weekEnd->format('M j, Y') }}</span>
        <span class="text-ink/50">(Monday to Sunday)</span>
    </p>

    <section class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Therapist summary">
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
            <p class="text-sm text-ink/60">Total Therapists</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-semibold text-ink">{{ $totalTherapists }}</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8h3M20.5 6.5v3"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">Registered therapists</p>
        </article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm">
            <p class="text-sm text-ink/60">Available Now</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-semibold text-ink">{{ $availableTherapists }}</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">Ready for an appointment</p>
        </article>
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <p class="text-sm text-ink/60">Total Commission This Week</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-semibold text-maroon">₱{{ number_format((float) $totalCommission, 2) }}</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/15 text-maroon">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
            </div>
            <p class="mt-1 text-xs text-ink/50">{{ number_format((float) config('spa.therapist_share') * 100, 0) }}% therapist portion of the 60/40 sharing arrangement</p>
        </article>
    </section>

    <section class="min-w-0 overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="therapists-heading">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-maroon/10 px-4 py-4 sm:px-5">
            <div>
                <h2 id="therapists-heading" class="text-base font-semibold text-ink">Therapist Directory</h2>
                <p class="mt-1 text-xs text-ink/55">Weekly completed services, sales, and commission</p>
            </div>
            <span class="text-xs text-ink/55">{{ $therapists->count() }} {{ \Illuminate\Support\Str::plural('therapist', $therapists->count()) }}</span>
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
                    @forelse ($therapists as $therapist)
                        <tr
                            data-therapist-row
                            data-therapist-id="{{ $therapist->id }}"
                            data-therapist-name="{{ $therapist->therapist_name }}"
                            data-phone="{{ $therapist->contact_number }}"
                            data-availability="{{ $therapist->availability_status }}"
                            data-completed-services="{{ $therapist->completed_services }}"
                            data-total-sales="{{ $therapist->total_sales ?? 0 }}"
                            data-commission="{{ $therapist->commission ?? 0 }}"
                            data-show-url="{{ route('therapists.show', $therapist) }}"
                            class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-4 py-4 font-medium text-ink">{{ $therapist->therapist_name }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-ink/75">{{ $therapist->contact_number ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium {{ $availabilityStyles[$therapist->availability_status] }}">
                                    {{ $availabilityLabels[$therapist->availability_status] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-ink/75">{{ $therapist->completed_services }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-medium text-ink">₱{{ number_format((float) $therapist->total_sales, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-semibold text-maroon">₱{{ number_format((float) $therapist->commission, 2) }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-therapist-action="view" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">View</button>
                                    <button type="button" data-therapist-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    <form method="POST" action="{{ route('therapists.destroy', $therapist) }}" data-confirm-delete data-confirm-message="Delete this therapist?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-ink/55">No therapists match your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div id="therapist-panel-overlay" class="fixed inset-0 z-40 hidden bg-ink/50" aria-hidden="true"></div>
    <aside id="therapist-panel" class="fixed inset-y-0 right-0 z-50 hidden w-full max-w-md translate-x-full overflow-y-auto border-l border-gold/25 bg-cream shadow-2xl transition-transform duration-300" aria-hidden="true" aria-labelledby="therapist-panel-title">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-maroon/10 bg-cream/95 px-5 py-4 backdrop-blur">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-ink/50">Therapist Profile</p>
                <h2 id="therapist-panel-title" class="mt-1 font-display text-3xl text-maroon">Therapist Profile</h2>
            </div>
            <button id="close-therapist-panel" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close therapist details">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-5 p-5">
            <p id="panel-load-error" class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert"></p>
            <section class="rounded-xl border border-maroon/10 bg-white p-4">
                <div class="flex items-center gap-3">
                    <span id="panel-initials" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold/20 text-sm font-semibold text-maroon"></span>
                    <div class="min-w-0 flex-1">
                        <p id="panel-phone" class="text-sm text-ink/65"></p>
                        <span id="panel-availability" class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium"></span>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-maroon/10 pt-4">
                    <div>
                        <p class="text-xs text-ink/55">Services Completed This Week</p>
                        <p id="panel-completed-count" class="mt-1 font-semibold text-ink">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink/55">Commission This Week</p>
                        <p id="panel-commission" class="mt-1 font-semibold text-maroon">₱0.00</p>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white">
                <div class="border-b border-maroon/10 px-4 py-3">
                    <h3 class="text-sm font-semibold text-ink">Upcoming Schedule</h3>
                    <p class="mt-0.5 text-xs text-ink/55">Schedules from today onward</p>
                </div>
                <div id="panel-schedule" class="divide-y divide-maroon/5"></div>
                <form id="schedule-form" method="POST" action="{{ route('therapist-schedules.store') }}" class="space-y-3 border-t border-maroon/10 bg-cream/45 p-4">
                    @csrf
                    <input type="hidden" name="therapist_id" id="schedule-therapist-id">
                    <input type="hidden" name="therapist_schedule_id" id="schedule-id">
                    <h4 id="schedule-form-title" class="text-sm font-semibold text-ink">Add Schedule</h4>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <label class="sm:col-span-1">
                            <span class="mb-1 block text-xs font-medium text-ink/65">Date</span>
                            <input name="schedule_date" id="schedule-date" type="date" min="{{ now()->toDateString() }}" required class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-2 text-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            @error('schedule_date')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="mb-1 block text-xs font-medium text-ink/65">Start</span>
                            <input name="start_time" id="schedule-start-time" type="time" required class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-2 text-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            @error('start_time')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <label>
                            <span class="mb-1 block text-xs font-medium text-ink/65">End</span>
                            <input name="end_time" id="schedule-end-time" type="time" required class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-2 text-sm outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            @error('end_time')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                        </label>
                    </div>
                    @error('therapist_id')<span class="block text-xs text-red-700">{{ $message }}</span>@enderror
                    <div class="flex justify-end gap-2">
                        <button id="cancel-schedule-edit" type="button" class="hidden rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-ink">Cancel edit</button>
                        <button id="save-schedule" type="submit" class="rounded-lg bg-gold px-4 py-2 text-xs font-semibold text-maroon transition hover:bg-gold/90">Add Schedule</button>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white">
                <div class="border-b border-maroon/10 px-4 py-3">
                    <h3 class="text-sm font-semibold text-ink">Completed Services</h3>
                    <p class="mt-0.5 text-xs text-ink/55">Latest 10 paid services</p>
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
            <form id="therapist-form" method="POST" action="{{ route('therapists.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input id="therapist-id" name="therapist_record_id" type="hidden" value="{{ old('therapist_record_id') }}">
                <div>
                    <label for="therapist-name" class="mb-1.5 block text-sm font-medium text-ink">Name</label>
                    <input id="therapist-name" name="therapist_name" type="text" required maxlength="100" value="{{ old('therapist_name') }}" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    @error('therapist_name')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="therapist-phone" class="mb-1.5 block text-sm font-medium text-ink">Contact Number</label>
                    <input id="therapist-phone" name="contact_number" type="tel" placeholder="e.g. 09171234567" value="{{ old('contact_number') }}" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                    @error('contact_number')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="therapist-availability" class="mb-1.5 block text-sm font-medium text-ink">Availability Status</label>
                    <select id="therapist-availability" name="availability_status" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                        <option value="available" @selected(old('availability_status', 'available') === 'available')>Available</option>
                        <option value="in_service" @selected(old('availability_status') === 'in_service')>In Service</option>
                        <option value="off_duty" @selected(old('availability_status') === 'off_duty')>Off Duty</option>
                    </select>
                    @error('availability_status')<span class="mt-1 block text-xs text-red-700">{{ $message }}</span>@enderror
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button id="cancel-therapist" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Therapist</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const currencyFormatter = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        const therapistTableBody = document.getElementById('therapist-table-body');
        const therapistPanel = document.getElementById('therapist-panel');
        const therapistPanelOverlay = document.getElementById('therapist-panel-overlay');
        const therapistModal = document.getElementById('therapist-modal');
        const therapistForm = document.getElementById('therapist-form');
        const scheduleForm = document.getElementById('schedule-form');
        const availabilityStyles = {
            available: 'bg-emerald-50 text-emerald-700',
            in_service: 'bg-sky-50 text-sky-700',
            off_duty: 'bg-ink/10 text-ink/65',
        };
        const availabilityLabels = {
            available: 'Available',
            in_service: 'In Service',
            off_duty: 'Off Duty',
        };
        const therapistUpdateUrl = @json(route('therapists.update', ['therapist' => '__THERAPIST__']));
        const scheduleUpdateUrl = @json(route('therapist-schedules.update', ['schedule' => '__SCHEDULE__']));
        const scheduleDeleteUrl = @json(route('therapist-schedules.destroy', ['schedule' => '__SCHEDULE__']));
        const csrfToken = @json(csrf_token());
        let modalTrigger = null;
        let activeTherapistId = null;

        function formatCurrency(amount) {
            return currencyFormatter.format(Number(amount) || 0);
        }

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

        function openTherapistEditor(therapist = null) {
            modalTrigger = document.activeElement;
            therapistForm.reset();
            const therapistId = therapist?.id ?? '';
            document.getElementById('therapist-id').value = therapistId;
            document.getElementById('therapist-modal-title').textContent = therapistId ? 'Edit Therapist' : 'Add Therapist';
            document.getElementById('therapist-name').value = therapist?.therapist_name ?? '';
            document.getElementById('therapist-phone').value = therapist?.contact_number ?? '';
            document.getElementById('therapist-availability').value = therapist?.availability_status ?? 'available';
            therapistForm.action = therapistId
                ? therapistUpdateUrl.replace('__THERAPIST__', encodeURIComponent(therapistId))
                : @json(route('therapists.store'));
            setMethod(therapistForm, therapistId ? 'PUT' : null);
            setTherapistModalOpen(true);
        }

        function setTherapistPanelOpen(isOpen) {
            therapistPanel.classList.toggle('hidden', !isOpen);
            therapistPanel.classList.toggle('translate-x-full', !isOpen);
            therapistPanelOverlay.classList.toggle('hidden', !isOpen);
            therapistPanel.setAttribute('aria-hidden', String(!isOpen));
            therapistPanelOverlay.setAttribute('aria-hidden', String(!isOpen));
        }

        function showEmptyMessage(container, message) {
            const emptyMessage = document.createElement('p');
            emptyMessage.className = 'p-4 text-sm text-ink/55';
            emptyMessage.textContent = message;
            container.replaceChildren(emptyMessage);
        }

        function renderSchedules(schedules) {
            const container = document.getElementById('panel-schedule');
            container.replaceChildren();

            if (schedules.length === 0) {
                showEmptyMessage(container, 'No schedule yet');

                return;
            }

            schedules.forEach((schedule) => {
                const item = document.createElement('article');
                item.className = 'flex items-start gap-3 p-4';
                const marker = document.createElement('span');
                marker.className = 'mt-1 h-2 w-2 shrink-0 rounded-full bg-gold';
                marker.setAttribute('aria-hidden', 'true');
                const content = document.createElement('div');
                content.className = 'min-w-0 flex-1';
                const date = document.createElement('p');
                date.className = 'text-sm font-medium text-ink';
                date.textContent = schedule.date;
                const time = document.createElement('p');
                time.className = 'mt-1 text-xs text-ink/55';
                time.textContent = `${schedule.start_label} - ${schedule.end_label}`;
                const actions = document.createElement('div');
                actions.className = 'mt-3 flex gap-2';
                const edit = document.createElement('button');
                edit.type = 'button';
                edit.dataset.editSchedule = '';
                edit.dataset.schedule = JSON.stringify(schedule);
                edit.className = 'rounded-lg border border-maroon/15 px-3 py-1.5 text-xs font-medium text-ink hover:border-gold hover:bg-gold/10';
                edit.textContent = 'Edit';
                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.action = scheduleDeleteUrl.replace('__SCHEDULE__', encodeURIComponent(schedule.id));
                deleteForm.dataset.confirmDelete = '';
                deleteForm.dataset.confirmMessage = 'Delete this schedule?';
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken;
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                const deleteButton = document.createElement('button');
                deleteButton.type = 'submit';
                deleteButton.className = 'rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50';
                deleteButton.textContent = 'Delete';
                deleteForm.append(token, method, deleteButton);
                actions.append(edit, deleteForm);
                content.append(date, time, actions);
                item.append(marker, content);
                container.appendChild(item);
            });
        }

        function renderCompletedServices(services) {
            const container = document.getElementById('panel-service-history');
            container.replaceChildren();

            if (services.length === 0) {
                showEmptyMessage(container, 'No completed services yet');

                return;
            }

            services.forEach((service) => {
                const item = document.createElement('article');
                item.className = 'flex items-start gap-3 p-4';
                const marker = document.createElement('span');
                marker.className = 'mt-1 h-2 w-2 shrink-0 rounded-full bg-gold';
                marker.setAttribute('aria-hidden', 'true');
                const content = document.createElement('div');
                content.className = 'min-w-0 flex-1';
                const heading = document.createElement('div');
                heading.className = 'flex items-start justify-between gap-2';
                const title = document.createElement('h4');
                title.className = 'text-sm font-medium text-ink';
                title.textContent = service.service_name;
                const subtotal = document.createElement('span');
                subtotal.className = 'whitespace-nowrap text-xs font-medium text-maroon';
                subtotal.textContent = formatCurrency(service.subtotal);
                heading.append(title, subtotal);
                const customer = document.createElement('p');
                customer.className = 'mt-1 text-xs text-ink/65';
                customer.textContent = service.customer_name;
                const details = document.createElement('p');
                details.className = 'mt-1 text-xs text-ink/55';
                details.textContent = `${service.date} · Commission ${formatCurrency(service.commission)}`;
                content.append(heading, customer, details);
                item.append(marker, content);
                container.appendChild(item);
            });
        }

        function openScheduleEditor(schedule = null) {
            scheduleForm.reset();
            document.getElementById('schedule-therapist-id').value = activeTherapistId;
            document.getElementById('schedule-id').value = schedule?.id ?? '';
            document.getElementById('schedule-date').value = schedule?.schedule_date ?? '';
            document.getElementById('schedule-start-time').value = schedule?.start_time?.slice(0, 5) ?? '';
            document.getElementById('schedule-end-time').value = schedule?.end_time?.slice(0, 5) ?? '';
            const isEditing = Boolean(schedule?.id);
            document.getElementById('schedule-date').min = isEditing ? '' : @json(now()->toDateString());
            scheduleForm.action = isEditing
                ? scheduleUpdateUrl.replace('__SCHEDULE__', encodeURIComponent(schedule.id))
                : @json(route('therapist-schedules.store'));
            setMethod(scheduleForm, isEditing ? 'PUT' : null);
            document.getElementById('schedule-form-title').textContent = isEditing ? 'Edit Schedule' : 'Add Schedule';
            document.getElementById('save-schedule').textContent = isEditing ? 'Save Changes' : 'Add Schedule';
            document.getElementById('cancel-schedule-edit').classList.toggle('hidden', !isEditing);
        }

        async function showTherapistDetails(row) {
            activeTherapistId = row.dataset.therapistId;
            const name = row.dataset.therapistName;
            const initials = name.split(/\s+/).map((part) => part[0]).join('').slice(0, 2).toUpperCase();
            const availability = row.dataset.availability;
            const availabilityBadge = document.getElementById('panel-availability');
            document.getElementById('therapist-panel-title').textContent = name;
            document.getElementById('panel-initials').textContent = initials;
            document.getElementById('panel-phone').textContent = row.dataset.phone || '-';
            document.getElementById('panel-completed-count').textContent = row.dataset.completedServices;
            document.getElementById('panel-commission').textContent = formatCurrency(row.dataset.commission);
            availabilityBadge.className = `mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${availabilityStyles[availability]}`;
            availabilityBadge.textContent = availabilityLabels[availability];
            document.getElementById('panel-load-error').classList.add('hidden');
            document.getElementById('schedule-therapist-id').value = activeTherapistId;
            document.getElementById('panel-schedule').textContent = 'Loading schedules...';
            document.getElementById('panel-service-history').textContent = 'Loading completed services...';
            setTherapistPanelOpen(true);

            try {
                const response = await fetch(row.dataset.showUrl, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(`Unable to load therapist details (${response.status}).`);
                }

                const details = await response.json();
                renderSchedules(details.schedules);
                renderCompletedServices(details.completed_services);

                return details;
            } catch (error) {
                const errorMessage = error instanceof Error ? error.message : 'Unable to load therapist details.';
                const errorElement = document.getElementById('panel-load-error');
                errorElement.textContent = errorMessage;
                errorElement.classList.remove('hidden');
                console.error(error);

                return null;
            }
        }

        document.getElementById('add-therapist').addEventListener('click', () => openTherapistEditor());
        document.getElementById('close-therapist-modal').addEventListener('click', () => setTherapistModalOpen(false));
        document.getElementById('cancel-therapist').addEventListener('click', () => setTherapistModalOpen(false));
        document.getElementById('close-therapist-panel').addEventListener('click', () => setTherapistPanelOpen(false));
        therapistPanelOverlay.addEventListener('click', () => setTherapistPanelOpen(false));
        document.getElementById('cancel-schedule-edit').addEventListener('click', () => openScheduleEditor());

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

            openTherapistEditor({
                id: row.dataset.therapistId,
                therapist_name: row.dataset.therapistName,
                contact_number: row.dataset.phone,
                availability_status: row.dataset.availability,
            });
        });

        document.getElementById('panel-schedule').addEventListener('click', (event) => {
            const editButton = event.target.closest('[data-edit-schedule]');

            if (editButton) {
                openScheduleEditor(JSON.parse(editButton.dataset.schedule));
            }
        });

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form[data-confirm-delete]');

            if (form && !window.confirm(form.dataset.confirmMessage)) {
                event.preventDefault();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (!therapistModal.classList.contains('hidden')) {
                    setTherapistModalOpen(false);
                } else if (!therapistPanel.classList.contains('hidden')) {
                    setTherapistPanelOpen(false);
                }
            }
        });

        @if ($errors->hasAny(['therapist_name', 'contact_number', 'availability_status']))
            openTherapistEditor({
                id: @json(old('therapist_record_id')),
                therapist_name: @json(old('therapist_name')),
                contact_number: @json(old('contact_number')),
                availability_status: @json(old('availability_status', 'available')),
            });
        @endif

        @if ($errors->hasAny(['schedule_date', 'start_time', 'end_time', 'therapist_id']) || (session('error') && old('therapist_id')))
            const failedTherapistId = @json(old('therapist_id'));
            const failedTherapistRow = [...document.querySelectorAll('[data-therapist-row]')]
                .find((row) => row.dataset.therapistId === String(failedTherapistId));

            if (failedTherapistRow) {
                showTherapistDetails(failedTherapistRow).then(() => {
                    openScheduleEditor({
                        id: @json(old('therapist_schedule_id')),
                        schedule_date: @json(old('schedule_date')),
                        start_time: @json(old('start_time')),
                        end_time: @json(old('end_time')),
                    });
                });
            }
        @endif
    </script>
@endsection
