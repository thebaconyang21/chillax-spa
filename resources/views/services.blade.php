@extends('layouts.app')

@section('title', 'Service Management')
@section('breadcrumb', 'Service Management')

@section('content')
    @php
        $services = [
            ['id' => 1, 'name' => 'Swedish Massage', 'duration' => '1 hr', 'happy_hour_price' => 300, 'regular_price' => 350],
            ['id' => 2, 'name' => 'Therapeutic Massage', 'duration' => '1.5 hr', 'happy_hour_price' => null, 'regular_price' => 500],
            ['id' => 3, 'name' => 'Foot Massage', 'duration' => '1 hr', 'happy_hour_price' => null, 'regular_price' => 400],
            ['id' => 4, 'name' => 'Combo (Swedish + 1 add-on)', 'duration' => '1 hr', 'happy_hour_price' => 400, 'regular_price' => null],
        ];
        $addons = [
            ['name' => 'Ventosa', 'price' => 150],
            ['name' => 'Hot Stone', 'price' => 150],
            ['name' => 'Ear Candle', 'price' => 150],
            ['name' => '30 Minutes Massage', 'price' => 200],
        ];
    @endphp

    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Service Management</h1>
            <p class="mt-1 text-sm text-ink/65">Manage spa services, durations, and pricing.</p>
        </div>
        <button id="add-service" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-gold px-4 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 sm:self-auto">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add Service
        </button>
    </div>

    <div class="mb-5 flex items-center gap-3 rounded-xl border border-gold/35 bg-gold/10 px-4 py-3.5 text-sm text-maroon">
        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        <p><span class="font-semibold">Happy Hour:</span> 11:00 AM - 4:00 PM</p>
    </div>

    <section class="mb-7 overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="services-heading">
        <div class="border-b border-maroon/10 px-5 py-4">
            <h2 id="services-heading" class="text-base font-semibold text-ink">Spa Services</h2>
            <p class="mt-1 text-xs text-ink/55">Current treatments and pricing</p>
        </div>
        <div class="min-w-0 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Service Name</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Duration</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Happy Hour Price</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Regular Price</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="services-table-body" class="divide-y divide-maroon/5">
                    @foreach ($services as $service)
                        <tr
                            data-service-row
                            data-service-id="{{ $service['id'] }}"
                            data-service-name="{{ $service['name'] }}"
                            data-duration="{{ $service['duration'] }}"
                            data-happy-price="{{ $service['happy_hour_price'] ?? '' }}"
                            data-regular-price="{{ $service['regular_price'] ?? '' }}"
                            class="transition hover:bg-cream/45">
                            <td class="px-5 py-4 font-medium text-ink">{{ $service['name'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $service['duration'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-maroon">
                                {{ $service['happy_hour_price'] === null ? '—' : '₱'.number_format($service['happy_hour_price']) }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">
                                {{ $service['regular_price'] === null ? '—' : '₱'.number_format($service['regular_price']) }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-service-action="edit" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    <button type="button" data-service-action="delete" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="addons-heading">
        <div class="border-b border-maroon/10 px-5 py-4">
            <h2 id="addons-heading" class="font-display text-3xl text-maroon">Add-ons</h2>
            <p class="mt-1 text-xs text-ink/55">Enhance a treatment with an optional add-on</p>
        </div>
        <div class="min-w-0 overflow-x-auto">
            <table class="w-full min-w-[420px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Add-on Name</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-maroon/5">
                    @foreach ($addons as $addon)
                        <tr class="transition hover:bg-cream/45">
                            <td class="px-5 py-4 font-medium text-ink">{{ $addon['name'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-medium text-maroon">₱{{ number_format($addon['price']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-lg rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="service-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="service-modal-title" class="text-lg font-semibold text-maroon">Add Service</h2>
                    <p class="mt-1 text-xs text-ink/55">Enter the treatment details and pricing.</p>
                </div>
                <button id="close-service-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="service-form" class="space-y-4 p-5 sm:p-6">
                <input id="service-id" type="hidden">
                <div>
                    <label for="service-name" class="mb-1.5 block text-sm font-medium text-ink">Service Name</label>
                    <input id="service-name" name="name" type="text" required class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="service-duration" class="mb-1.5 block text-sm font-medium text-ink">Duration</label>
                    <input id="service-duration" name="duration" type="text" required placeholder="e.g. 1 hr" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="happy-hour-price" class="mb-1.5 block text-sm font-medium text-ink">Happy Hour Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                            <input id="happy-hour-price" name="happy_hour_price" type="number" min="0" step="0.01" placeholder="Optional" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                        </div>
                    </div>
                    <div>
                        <label for="regular-price" class="mb-1.5 block text-sm font-medium text-ink">Regular Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                            <input id="regular-price" name="regular_price" type="number" min="0" step="0.01" placeholder="Optional" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button id="cancel-service" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Service</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const serviceModal = document.getElementById('service-modal');
        const serviceForm = document.getElementById('service-form');
        const servicesTableBody = document.getElementById('services-table-body');
        const serviceNameInput = document.getElementById('service-name');
        let nextServiceId = Math.max(...[...document.querySelectorAll('[data-service-row]')].map((row) => Number(row.dataset.serviceId))) + 1;

        function setServiceModalOpen(isOpen) {
            serviceModal.classList.toggle('hidden', !isOpen);
            serviceModal.classList.toggle('flex', isOpen);
            serviceModal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                serviceNameInput.focus();
            }
        }

        function formatPrice(price) {
            return price === '' || price === null
                ? '—'
                : `₱${Number(price).toLocaleString('en-PH', { maximumFractionDigits: 2 })}`;
        }

        function createServiceRow(service) {
            const row = document.createElement('tr');
            row.dataset.serviceRow = '';
            row.dataset.serviceId = service.id;
            row.dataset.serviceName = service.name;
            row.dataset.duration = service.duration;
            row.dataset.happyPrice = service.happyHourPrice;
            row.dataset.regularPrice = service.regularPrice;
            row.className = 'transition hover:bg-cream/45';

            [service.name, service.duration, formatPrice(service.happyHourPrice), formatPrice(service.regularPrice)].forEach((value, index) => {
                const cell = document.createElement('td');
                cell.className = index === 0
                    ? 'px-5 py-4 font-medium text-ink'
                    : `px-5 py-4 ${index > 1 ? 'whitespace-nowrap font-medium text-maroon' : 'whitespace-nowrap text-ink/75'}`;
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actionsCell = document.createElement('td');
            actionsCell.className = 'px-5 py-4';

            const actions = document.createElement('div');
            actions.className = 'flex justify-end gap-2';

            [['edit', 'Edit', 'border-maroon/15 text-maroon hover:border-gold hover:bg-gold/10'], ['delete', 'Delete', 'border-red-200 text-red-700 hover:bg-red-50']].forEach(([action, label, styles]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.serviceAction = action;
                button.className = `rounded-lg border px-3 py-2 text-xs font-medium transition ${styles}`;
                button.textContent = label;
                actions.appendChild(button);
            });

            actionsCell.appendChild(actions);
            row.appendChild(actionsCell);

            return row;
        }

        function openServiceEditor(service = null) {
            serviceForm.reset();
            document.getElementById('service-id').value = service?.id ?? '';
            document.getElementById('service-modal-title').textContent = service ? 'Edit Service' : 'Add Service';
            serviceNameInput.value = service?.name ?? '';
            document.getElementById('service-duration').value = service?.duration ?? '';
            document.getElementById('happy-hour-price').value = service?.happyHourPrice ?? '';
            document.getElementById('regular-price').value = service?.regularPrice ?? '';
            setServiceModalOpen(true);
        }

        document.getElementById('add-service').addEventListener('click', () => openServiceEditor());
        document.getElementById('close-service-modal').addEventListener('click', () => setServiceModalOpen(false));
        document.getElementById('cancel-service').addEventListener('click', () => setServiceModalOpen(false));
        serviceModal.addEventListener('click', (event) => {
            if (event.target === serviceModal) {
                setServiceModalOpen(false);
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !serviceModal.classList.contains('hidden')) {
                setServiceModalOpen(false);
            }
        });

        servicesTableBody.addEventListener('click', (event) => {
            const actionButton = event.target.closest('[data-service-action]');

            if (!actionButton) {
                return;
            }

            const row = actionButton.closest('[data-service-row]');

            if (actionButton.dataset.serviceAction === 'edit') {
                openServiceEditor({
                    id: row.dataset.serviceId,
                    name: row.dataset.serviceName,
                    duration: row.dataset.duration,
                    happyHourPrice: row.dataset.happyPrice,
                    regularPrice: row.dataset.regularPrice,
                });

                return;
            }

            if (window.confirm(`Delete "${row.dataset.serviceName}"?`)) {
                row.remove();
            }
        });

        serviceForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const serviceId = document.getElementById('service-id').value;
            const service = {
                id: serviceId || nextServiceId++,
                name: serviceNameInput.value.trim(),
                duration: document.getElementById('service-duration').value.trim(),
                happyHourPrice: document.getElementById('happy-hour-price').value,
                regularPrice: document.getElementById('regular-price').value,
            };
            const existingRow = serviceId
                ? servicesTableBody.querySelector(`[data-service-id="${serviceId}"]`)
                : null;
            const newRow = createServiceRow(service);

            if (existingRow) {
                existingRow.replaceWith(newRow);
            } else {
                servicesTableBody.appendChild(newRow);
            }

            setServiceModalOpen(false);
        });
    </script>
@endsection
