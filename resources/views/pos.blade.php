@extends('layouts.app')

@section('title', 'Point of Sale')
@section('breadcrumb', 'Point of Sale (POS)')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div><h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Point of Sale</h1><p class="mt-1 text-sm text-ink/65">Create a spa service order and process payment.</p></div>
    <div class="rounded-full border border-gold/40 bg-gold/10 px-4 py-2 text-xs font-medium text-maroon" aria-live="polite">{{ $happyHourActive ? "Happy Hour rate applies until {$happyHourEnd}" : 'Regular rates' }}</div>
</div>

@if ($appointment)
    <div class="mb-5 rounded-xl border border-gold/50 bg-gold/10 p-4 text-sm text-maroon"><strong>Appointment:</strong> {{ $appointment->customer->customer_name }}, {{ $appointment->service->service_name }}, {{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</div>
@endif
@if (session('error')) <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div> @endif
@if ($errors->any()) <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert"><p class="font-semibold">Please correct the following:</p><ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

<form id="pos-form" method="POST" action="{{ route('pos.store') }}" class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.8fr)]">
    @csrf
    @if ($appointment)<input type="hidden" name="appointment_id" value="{{ $appointment->id }}">@endif
    <div class="min-w-0 space-y-5">
        <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5">
            <h2 class="mb-4 text-base font-semibold text-ink">Transaction Details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-ink" for="customer_id">Customer</label>
                    @if ($appointment)
                        <input type="hidden" name="customer_id" value="{{ $appointment->customer_id }}">
                        <div class="rounded-lg border border-maroon/15 bg-cream/50 px-3 py-2.5 text-sm">{{ $appointment->customer->customer_name }} <span class="text-ink/55">{{ $appointment->customer->contact_number }}</span></div>
                    @else
                        <div id="existing-customer"><select id="customer_id" name="customer_id" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm"><option value="">Select customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->customer_name }}{{ $customer->contact_number ? ' · '.$customer->contact_number : '' }}</option>@endforeach</select></div>
                        <div id="new-customer" class="mt-2 hidden space-y-2"><input name="new_customer_name" value="{{ old('new_customer_name') }}" placeholder="Customer name" class="h-11 w-full rounded-lg border border-maroon/15 px-3 text-sm"><input name="new_customer_contact" value="{{ old('new_customer_contact') }}" placeholder="Contact number" class="h-11 w-full rounded-lg border border-maroon/15 px-3 text-sm"></div>
                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-ink/70"><input id="new-customer-toggle" type="checkbox" @checked(old('new_customer_name')) class="rounded border-maroon/30 text-maroon"> Add a new customer</label>
                    @endif
                </div>
                <div class="rounded-lg bg-cream/70 p-3 text-sm text-ink/70"><span class="font-medium text-ink">Pricing</span><p class="mt-1">Prices are checked again when payment is processed.</p><p class="mt-1">Happy Hour: {{ $happyHourStart }}–{{ $happyHourEnd }}</p></div>
            </div>
        </section>

        <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4 flex items-center justify-between"><div><h2 class="text-base font-semibold text-ink">Services</h2><p class="mt-1 text-xs text-ink/55">Choose a service and therapist for each line.</p></div>@unless($appointment)<button type="button" id="add-line" class="rounded-lg border border-gold px-3 py-2 text-xs font-semibold text-maroon hover:bg-gold/10">+ Add another service</button>@endunless</div>
            <div id="service-lines" class="space-y-4">
                <div class="pos-line rounded-xl border border-maroon/10 bg-cream/30 p-4" data-line="0">
                    <div class="mb-3 flex items-center justify-between"><span class="line-heading text-sm font-semibold text-maroon">Service 1</span>@unless($appointment)<button type="button" class="remove-line text-xs text-red-600">Remove</button>@endunless</div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="text-xs font-medium text-ink/70">Service
                            <select name="lines[0][service_id]" class="line-service mt-1 h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm" @if($appointment) disabled @endif>
                                <option value="">Select service</option>@foreach ($services as $service)<option value="{{ $service->id }}" data-name="{{ $service->service_name }}" data-duration="{{ $service->duration }}" data-price="{{ $appointment && $appointment->service_id === $service->id ? $appointment->reserved_price : ($service->current_price ?? '') }}" data-offered="{{ $service->current_price !== null ? '1' : '0' }}" @selected($appointment?->service_id === $service->id || old('lines.0.service_id') == $service->id) @disabled($service->current_price === null && ! $appointment)>{{ $service->service_name }} · {{ $service->duration }} min · {{ $service->current_price === null ? 'Not offered now' : '₱'.number_format($service->current_price, 2) }}</option>@endforeach
                            </select>
                            @if($appointment)<input type="hidden" name="lines[0][service_id]" value="{{ $appointment->service_id }}">@endif
                        </label>
                        <label class="text-xs font-medium text-ink/70">Therapist
                            @if($appointment)<input type="hidden" name="lines[0][therapist_id]" value="{{ $appointment->therapist_id }}"><div class="mt-1 rounded-lg border border-maroon/15 bg-white px-3 py-3 text-sm">{{ $appointment->therapist->therapist_name }}</div>@else
                            <select name="lines[0][therapist_id]" class="line-therapist mt-1 h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm"><option value="">Select therapist</option>@foreach ($therapists as $therapist)<option value="{{ $therapist->id }}" @selected(old('lines.0.therapist_id') == $therapist->id)>{{ $therapist->therapist_name }}</option>@endforeach</select>@endif
                        </label>
                    </div>
                    @unless($appointment)<div class="service-cards mt-3 grid gap-2 sm:grid-cols-2">@foreach ($services as $service)<button type="button" data-service-card="{{ $service->id }}" @disabled($service->current_price === null) class="rounded-lg border border-maroon/15 bg-white p-3 text-left text-xs transition hover:border-gold disabled:cursor-not-allowed disabled:opacity-50"><span class="block font-semibold text-ink">{{ $service->service_name }}</span><span class="mt-1 block text-ink/55">{{ $service->duration }} min · {{ $service->current_price === null ? 'Not offered now' : '₱'.number_format($service->current_price, 2) }}</span></button>@endforeach</div>@endunless
                    <div class="mt-4"><p class="mb-2 text-xs font-medium text-ink/70">Add-ons</p><div class="grid gap-2 sm:grid-cols-2">@foreach ($addons as $addon)<label class="flex items-center justify-between gap-2 rounded-lg border border-maroon/10 bg-white px-3 py-2 text-xs"><span class="flex items-center gap-2"><input type="checkbox" name="lines[0][addons][]" value="{{ $addon->id }}" data-addon-price="{{ $addon->price }}" data-addon-name="{{ $addon->addon_name }}" class="line-addon rounded border-maroon/30 text-maroon" @checked(in_array($addon->id, old('lines.0.addons', [])))><span>{{ $addon->addon_name }}</span></span><span>₱{{ number_format($addon->price, 2) }}</span></label>@endforeach</div></div>
                </div>
            </div>
            <template id="line-template"><div class="pos-line rounded-xl border border-maroon/10 bg-cream/30 p-4" data-line="__INDEX__"><div class="mb-3 flex items-center justify-between"><span class="line-heading text-sm font-semibold text-maroon"></span><button type="button" class="remove-line text-xs text-red-600">Remove</button></div><div class="grid gap-3 sm:grid-cols-2"><label class="text-xs font-medium text-ink/70">Service<select name="lines[__INDEX__][service_id]" class="line-service mt-1 h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm"><option value="">Select service</option>@foreach ($services as $service)<option value="{{ $service->id }}" data-name="{{ $service->service_name }}" data-duration="{{ $service->duration }}" data-price="{{ $service->current_price ?? '' }}" data-offered="{{ $service->current_price !== null ? '1' : '0' }}" @disabled($service->current_price === null)>{{ $service->service_name }} · {{ $service->duration }} min · {{ $service->current_price === null ? 'Not offered now' : '₱'.number_format($service->current_price, 2) }}</option>@endforeach</select></label><label class="text-xs font-medium text-ink/70">Therapist<select name="lines[__INDEX__][therapist_id]" class="line-therapist mt-1 h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm"><option value="">Select therapist</option>@foreach ($therapists as $therapist)<option value="{{ $therapist->id }}">{{ $therapist->therapist_name }}</option>@endforeach</select></label></div><div class="service-cards mt-3 grid gap-2 sm:grid-cols-2">@foreach ($services as $service)<button type="button" data-service-card="{{ $service->id }}" @disabled($service->current_price === null) class="rounded-lg border border-maroon/15 bg-white p-3 text-left text-xs transition hover:border-gold disabled:cursor-not-allowed disabled:opacity-50"><span class="block font-semibold text-ink">{{ $service->service_name }}</span><span class="mt-1 block text-ink/55">{{ $service->duration }} min · {{ $service->current_price === null ? 'Not offered now' : '₱'.number_format($service->current_price, 2) }}</span></button>@endforeach</div><div class="mt-4"><p class="mb-2 text-xs font-medium text-ink/70">Add-ons</p><div class="grid gap-2 sm:grid-cols-2">@foreach ($addons as $addon)<label class="flex items-center justify-between gap-2 rounded-lg border border-maroon/10 bg-white px-3 py-2 text-xs"><span class="flex items-center gap-2"><input type="checkbox" name="lines[__INDEX__][addons][]" value="{{ $addon->id }}" data-addon-price="{{ $addon->price }}" data-addon-name="{{ $addon->addon_name }}" class="line-addon rounded border-maroon/30 text-maroon"><span>{{ $addon->addon_name }}</span></span><span>₱{{ number_format($addon->price, 2) }}</span></label>@endforeach</div></div></div></template>
        </section>
    </div>

    <aside class="space-y-5">
        <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5"><h2 class="text-base font-semibold text-ink">Order Summary</h2><div id="summary-lines" class="mt-4 space-y-3 text-sm"><p class="text-ink/50">Choose a service to begin.</p></div><div class="mt-4 flex justify-between border-t border-maroon/10 pt-4 text-lg font-bold text-maroon"><span>Total</span><span id="total-display">₱0.00</span></div>
            <div class="mt-5 space-y-3"><label class="block text-sm font-medium text-ink">Payment method<select id="payment-method" name="payment_method" class="mt-1 h-11 w-full rounded-lg border border-maroon/15 bg-white px-3"><option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option><option value="ecash" @selected(old('payment_method') === 'ecash')>E-cash</option></select></label><label class="block text-sm font-medium text-ink">Amount Paid<input id="amount-paid" name="amount_paid" type="number" min="0" step="0.01" value="{{ old('amount_paid', '0.00') }}" class="mt-1 h-11 w-full rounded-lg border border-maroon/15 px-3"></label><div id="short-warning" class="hidden rounded-lg bg-red-50 p-3 text-sm font-medium text-red-700">Amount paid is less than the total.</div><div class="flex justify-between rounded-lg bg-cream/70 px-3 py-3 text-sm"><span>Change</span><strong id="change-display">₱0.00</strong></div></div>
            <button id="process-payment" type="submit" class="mt-5 w-full rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-maroon/90 disabled:cursor-not-allowed disabled:opacity-50">Process Payment</button><button type="button" disabled class="mt-2 w-full rounded-lg border border-maroon/15 px-4 py-3 text-sm font-semibold text-ink/40">Print Receipt</button>
        </section>
    </aside>
</form>

<script>
(() => {
    const form = document.getElementById('pos-form');
    const lines = document.getElementById('service-lines');
    const summary = document.getElementById('summary-lines');
    const paymentMethod = document.getElementById('payment-method');
    const amountPaid = document.getElementById('amount-paid');
    const includedCombo = 'Combo (Swedish Massage + 1 Add-on)';
    let nextIndex = 1;
    const money = value => '₱' + Number(value || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const selectedOption = element => element?.selectedOptions?.[0];

    const oldLines = @json(old('lines', []));
    if (Array.isArray(oldLines) && oldLines.length) {
        const restoreLine = (element, values) => {
            element.querySelector('.line-service').value = values.service_id || '';
            const therapist = element.querySelector('.line-therapist');
            if (therapist) therapist.value = values.therapist_id || '';
            const selectedAddons = (values.addons || []).map(String);
            element.querySelectorAll('.line-addon').forEach(box => box.checked = selectedAddons.includes(box.value));
        };
        const initial = lines.querySelector('.pos-line');
        restoreLine(initial, oldLines[0]);
        for (let index = 1; index < oldLines.length; index++) {
            const markup = document.getElementById('line-template').innerHTML.replaceAll('__INDEX__', String(index));
            lines.insertAdjacentHTML('beforeend', markup);
            restoreLine(lines.querySelectorAll('.pos-line')[index], oldLines[index]);
        }
        nextIndex = oldLines.length;
    }

    function update() {
        let total = 0;
        const rows = [];
        lines.querySelectorAll('.pos-line').forEach((line, index) => {
            line.querySelector('.line-heading').textContent = 'Service ' + (index + 1);
            const serviceSelect = line.querySelector('.line-service');
            const service = selectedOption(serviceSelect);
            const name = service?.dataset.name;
            const price = Number(service?.dataset.price || 0);
            const selectedAddons = [...line.querySelectorAll('.line-addon:checked')];
            const combo = name === includedCombo;
            let addonsTotal = 0;
            const addonRows = selectedAddons.map((box, addonIndex) => {
                const included = combo && addonIndex === 0;
                const addonPrice = included ? 0 : Number(box.dataset.addonPrice);
                addonsTotal += addonPrice;
                return `<div class="flex justify-between pl-3 text-xs text-ink/60"><span>${box.dataset.addonName}${included ? ' · Included' : ''}</span><span>${money(addonPrice)}</span></div>`;
            }).join('');
            if (name) {
                const lineTotal = price + addonsTotal;
                total += lineTotal;
                rows.push(`<div class="border-b border-maroon/10 pb-3"><div class="flex justify-between font-medium"><span>${name}</span><span>${money(price)}</span></div><p class="mt-1 text-xs text-ink/55">${service.dataset.duration} min${combo ? ' · 1 add-on included' : ''}</p>${addonRows}<div class="mt-1 flex justify-between text-xs font-semibold"><span>Line total</span><span>${money(lineTotal)}</span></div></div>`);
            }
        });
        summary.innerHTML = rows.length ? rows.join('') : '<p class="text-ink/50">Choose a service to begin.</p>';
        lines.querySelectorAll('.pos-line').forEach(line => {
            const selectedId = line.querySelector('.line-service').value;
            line.querySelectorAll('[data-service-card]').forEach(card => {
                const selected = card.dataset.serviceCard === selectedId;
                card.classList.toggle('border-gold', selected);
                card.classList.toggle('bg-gold/10', selected);
                card.setAttribute('aria-pressed', String(selected));
            });
        });
        document.getElementById('total-display').textContent = money(total);
        if (paymentMethod.value === 'ecash') amountPaid.value = total.toFixed(2);
        const paid = Number(amountPaid.value || 0);
        const short = paid < total;
        const exactRequired = paymentMethod.value === 'ecash' && paid !== total;
        const warning = document.getElementById('short-warning');
        warning.textContent = paymentMethod.value === 'ecash' && exactRequired ? 'E-cash amount paid must equal the total.' : 'Amount paid is less than the total.';
        warning.classList.toggle('hidden', !(short || exactRequired));
        document.getElementById('change-display').textContent = money(paymentMethod.value === 'cash' ? Math.max(0, paid - total) : 0);
        document.getElementById('process-payment').disabled = total <= 0 || short || exactRequired;
    }

    document.getElementById('add-line')?.addEventListener('click', () => {
        const markup = document.getElementById('line-template').innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        lines.insertAdjacentHTML('beforeend', markup);
        update();
    });
    lines.addEventListener('click', event => {
        const card = event.target.closest('[data-service-card]');
        if (card) {
            const line = card.closest('.pos-line');
            line.querySelector('.line-service').value = card.dataset.serviceCard;
            update();
        }
        if (event.target.matches('.remove-line') && lines.querySelectorAll('.pos-line').length > 1) {
            event.target.closest('.pos-line').remove(); update();
        }
    });
    lines.addEventListener('change', update);
    paymentMethod.addEventListener('change', update);
    amountPaid.addEventListener('input', update);
    document.getElementById('new-customer-toggle')?.addEventListener('change', event => {
        document.getElementById('new-customer').classList.toggle('hidden', !event.target.checked);
        document.getElementById('existing-customer').classList.toggle('hidden', event.target.checked);
        const select = document.getElementById('customer_id');
        if (select) select.disabled = event.target.checked;
        document.querySelector('[name="new_customer_name"]').disabled = !event.target.checked;
        document.querySelector('[name="new_customer_contact"]').disabled = !event.target.checked;
    });
    const newCustomer = document.getElementById('new-customer-toggle');
    if (newCustomer) {
        const enabled = newCustomer.checked;
        document.getElementById('new-customer').classList.toggle('hidden', !enabled);
        document.getElementById('existing-customer').classList.toggle('hidden', enabled);
        document.getElementById('customer_id').disabled = enabled;
        document.querySelector('[name="new_customer_name"]').disabled = !enabled;
        document.querySelector('[name="new_customer_contact"]').disabled = !enabled;
    }
    form.addEventListener('submit', event => {
        const button = document.getElementById('process-payment');
        if (button.disabled) { event.preventDefault(); return; }
        button.disabled = true; button.textContent = 'Processing…';
    });
    update();
})();
</script>
@endsection
