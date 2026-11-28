@extends('layouts.app')

@section('title', 'Service Management')
@section('breadcrumb', 'Service Management')

@section('content')
    @if (session('success'))
        <div role="status" class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    @forelse ($services as $service)
                        @php
                            $durationHours = $service->duration / 60;
                            $durationLabel = $service->duration < 60
                                ? $service->duration.' mins'
                                : ($durationHours === 1.0
                                    ? '1 hr'
                                    : rtrim(rtrim(number_format($durationHours, 2, '.', ''), '0'), '.').' hrs');
                        @endphp
                        <tr
                            data-service-row
                            data-service-id="{{ $service->id }}"
                            data-service-name="{{ $service->service_name }}"
                            data-duration="{{ $service->duration }}"
                            data-happy-price="{{ $service->happy_hour_price ?? '' }}"
                            data-regular-price="{{ $service->regular_price ?? '' }}"
                            class="transition hover:bg-cream/45">
                            <td class="px-5 py-4 font-medium text-ink">{{ $service->service_name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $durationLabel }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-maroon">
                                {{ $service->happy_hour_price === null ? '-' : '₱'.number_format((float) $service->happy_hour_price, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">
                                {{ $service->regular_price === null ? '-' : '₱'.number_format((float) $service->regular_price, 2) }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-service-action="edit" data-confirm-edit data-record-name="{{ $service->service_name }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    <form method="POST" action="{{ route('services.destroy', $service) }}" data-confirm-delete data-record-name="{{ $service->service_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-sm text-ink/55">No services have been added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="addons-heading">
        <div class="flex flex-col justify-between gap-3 border-b border-maroon/10 px-5 py-4 sm:flex-row sm:items-center">
            <div>
                <h2 id="addons-heading" class="font-display text-3xl text-maroon">Add-ons</h2>
                <p class="mt-1 text-xs text-ink/55">Enhance a treatment with an optional add-on</p>
            </div>
            <button id="add-addon" type="button" class="inline-flex min-h-10 items-center justify-center gap-2 self-start rounded-lg bg-gold px-4 py-2 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 sm:self-auto">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add Add-on
            </button>
        </div>
        <div class="min-w-0 overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Add-on Name</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Price</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="addons-table-body" class="divide-y divide-maroon/5">
                    @forelse ($addons as $addon)
                        <tr data-addon-row data-addon-id="{{ $addon->id }}" data-addon-name="{{ $addon->addon_name }}" data-addon-price="{{ $addon->price }}" class="transition hover:bg-cream/45">
                            <td class="px-5 py-4 font-medium text-ink">{{ $addon->addon_name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-medium text-maroon">₱{{ number_format((float) $addon->price, 2) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-addon-action="edit" data-confirm-edit data-record-name="{{ $addon->addon_name }}" class="rounded-lg border border-maroon/15 px-3 py-2 text-xs font-medium text-maroon transition hover:border-gold hover:bg-gold/10">Edit</button>
                                    <form method="POST" action="{{ route('addons.destroy', $addon) }}" data-confirm-delete data-record-name="{{ $addon->addon_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-6 text-center text-sm text-ink/55">No add-ons have been added yet.</td>
                        </tr>
                    @endforelse
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

            <form id="service-form" method="POST" action="{{ route('services.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input id="service-id" name="service_id" type="hidden" value="{{ old('service_id') }}">
                <div>
                    <label for="service-name" class="mb-1.5 block text-sm font-medium text-ink">Service Name</label>
                    <input id="service-name" name="service_name" type="text" value="{{ old('service_name') }}" required maxlength="100" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="service-duration" class="mb-1.5 block text-sm font-medium text-ink">Duration (minutes)</label>
                    <input id="service-duration" name="duration" type="number" value="{{ old('duration') }}" required min="1" step="1" placeholder="e.g. 60" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="happy-hour-price" class="mb-1.5 block text-sm font-medium text-ink">Happy Hour Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                            <input id="happy-hour-price" name="happy_hour_price" type="number" value="{{ old('happy_hour_price') }}" min="0" step="0.01" placeholder="Optional" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                        </div>
                    </div>
                    <div>
                        <label for="regular-price" class="mb-1.5 block text-sm font-medium text-ink">Regular Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                            <input id="regular-price" name="regular_price" type="number" value="{{ old('regular_price') }}" min="0" step="0.01" placeholder="Optional" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
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

    <div id="addon-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-lg rounded-2xl border border-gold/40 bg-cream shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="addon-modal-title">
            <div class="flex items-start justify-between gap-4 border-b border-maroon/10 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="addon-modal-title" class="text-lg font-semibold text-maroon">Add Add-on</h2>
                    <p class="mt-1 text-xs text-ink/55">Enter the add-on details and price.</p>
                </div>
                <button id="close-addon-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-ink/60 transition hover:bg-maroon/5 hover:text-maroon focus:outline-none focus:ring-2 focus:ring-gold" aria-label="Close dialog">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="addon-form" method="POST" action="{{ route('addons.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input id="addon-id" name="addon_id" type="hidden" value="{{ old('addon_id') }}">
                <div>
                    <label for="addon-name" class="mb-1.5 block text-sm font-medium text-ink">Add-on Name</label>
                    <input id="addon-name" name="addon_name" type="text" value="{{ old('addon_name') }}" required maxlength="100" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
                <div>
                    <label for="addon-price" class="mb-1.5 block text-sm font-medium text-ink">Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                        <input id="addon-price" name="price" type="number" value="{{ old('price') }}" required min="0" step="0.01" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button id="cancel-addon" type="button" class="min-h-11 rounded-lg border border-maroon/15 px-5 py-2.5 text-sm font-medium text-ink transition hover:bg-maroon/5">Cancel</button>
                    <button type="submit" class="min-h-11 rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">Save Add-on</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const serviceModal = document.getElementById('service-modal');
        const serviceForm = document.getElementById('service-form');
        const serviceUpdateUrlTemplate = @js(route('services.update', ['service' => '__SERVICE__']));
        const addonModal = document.getElementById('addon-modal');
        const addonForm = document.getElementById('addon-form');
        const addonUpdateUrlTemplate = @js(route('addons.update', ['addon' => '__ADDON__']));

        function setModalOpen(modal, isOpen, input) {
            modal.classList.toggle('hidden', !isOpen);
            modal.classList.toggle('flex', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));

            if (isOpen) {
                input.focus();
            }
        }

        function setMethodOverride(form, method) {
            let methodInput = form.querySelector('input[name="_method"]');

            if (method) {
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }

                methodInput.value = method;
            } else if (methodInput) {
                methodInput.remove();
            }
        }

        function findServiceRow(id) {
            return [...document.querySelectorAll('[data-service-row]')]
                .find((row) => row.dataset.serviceId === String(id));
        }

        function openServiceEditor(service = null) {
            serviceForm.reset();
            document.getElementById('service-id').value = service?.id ?? '';
            document.getElementById('service-modal-title').textContent = service ? 'Edit Service' : 'Add Service';
            document.getElementById('service-name').value = service?.name ?? '';
            document.getElementById('service-duration').value = service?.duration ?? '';
            document.getElementById('happy-hour-price').value = service?.happyHourPrice ?? '';
            document.getElementById('regular-price').value = service?.regularPrice ?? '';
            serviceForm.action = service
                ? serviceUpdateUrlTemplate.replace('__SERVICE__', encodeURIComponent(service.id))
                : @js(route('services.store'));
            setMethodOverride(serviceForm, service ? 'PUT' : null);
            setModalOpen(serviceModal, true, document.getElementById('service-name'));
        }

        function openAddonEditor(addon = null) {
            addonForm.reset();
            document.getElementById('addon-id').value = addon?.id ?? '';
            document.getElementById('addon-modal-title').textContent = addon ? 'Edit Add-on' : 'Add Add-on';
            document.getElementById('addon-name').value = addon?.name ?? '';
            document.getElementById('addon-price').value = addon?.price ?? '';
            addonForm.action = addon
                ? addonUpdateUrlTemplate.replace('__ADDON__', encodeURIComponent(addon.id))
                : @js(route('addons.store'));
            setMethodOverride(addonForm, addon ? 'PUT' : null);
            setModalOpen(addonModal, true, document.getElementById('addon-name'));
        }

        document.getElementById('add-service').addEventListener('click', () => openServiceEditor());
        document.getElementById('close-service-modal').addEventListener('click', () => setModalOpen(serviceModal, false));
        document.getElementById('cancel-service').addEventListener('click', () => setModalOpen(serviceModal, false));
        document.getElementById('add-addon').addEventListener('click', () => openAddonEditor());
        document.getElementById('close-addon-modal').addEventListener('click', () => setModalOpen(addonModal, false));
        document.getElementById('cancel-addon').addEventListener('click', () => setModalOpen(addonModal, false));

        document.getElementById('services-table-body').addEventListener('click', (event) => {
            const button = event.target.closest('[data-service-action="edit"]');

            if (!button) {
                return;
            }

            const row = button.closest('[data-service-row]');

            openServiceEditor({
                id: row.dataset.serviceId,
                name: row.dataset.serviceName,
                duration: row.dataset.duration,
                happyHourPrice: row.dataset.happyPrice,
                regularPrice: row.dataset.regularPrice,
            });
        });

        document.getElementById('addons-table-body').addEventListener('click', (event) => {
            const button = event.target.closest('[data-addon-action="edit"]');

            if (!button) {
                return;
            }

            const row = button.closest('[data-addon-row]');

            openAddonEditor({
                id: row.dataset.addonId,
                name: row.dataset.addonName,
                price: row.dataset.addonPrice,
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setModalOpen(serviceModal, false);
                setModalOpen(addonModal, false);
            }
        });

        @if ($errors->hasAny(['service_name', 'duration', 'happy_hour_price', 'regular_price']))
            const failedServiceRow = findServiceRow(@js(old('service_id')));

            openServiceEditor(failedServiceRow ? {
                id: failedServiceRow.dataset.serviceId,
                name: failedServiceRow.dataset.serviceName,
                duration: failedServiceRow.dataset.duration,
                happyHourPrice: failedServiceRow.dataset.happyPrice,
                regularPrice: failedServiceRow.dataset.regularPrice,
            } : null);
            document.getElementById('service-name').value = @js(old('service_name', ''));
            document.getElementById('service-duration').value = @js(old('duration', ''));
            document.getElementById('happy-hour-price').value = @js(old('happy_hour_price', ''));
            document.getElementById('regular-price').value = @js(old('regular_price', ''));
        @elseif ($errors->hasAny(['addon_name', 'price']))
            const failedAddonId = @js(old('addon_id'));
            const failedAddonRow = [...document.querySelectorAll('[data-addon-row]')]
                .find((row) => row.dataset.addonId === String(failedAddonId));

            openAddonEditor(failedAddonRow ? {
                id: failedAddonRow.dataset.addonId,
                name: failedAddonRow.dataset.addonName,
                price: failedAddonRow.dataset.addonPrice,
            } : null);
            document.getElementById('addon-name').value = @js(old('addon_name', ''));
            document.getElementById('addon-price').value = @js(old('price', ''));
        @endif
    </script>
@endsection
