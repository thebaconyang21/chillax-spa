@extends('layouts.app')

@section('title', 'Point of Sale')
@section('breadcrumb', 'Point of Sale (POS)')

@section('content')
    @php
        $customers = [
            ['id' => 1, 'name' => 'Maria Santos', 'phone' => '0917 123 4567'],
            ['id' => 2, 'name' => 'Ana Reyes', 'phone' => '0917 555 0142'],
            ['id' => 3, 'name' => 'Liza Cruz', 'phone' => '0920 444 7621'],
            ['id' => 4, 'name' => 'John Dela Cruz', 'phone' => '0916 333 9081'],
        ];
        $therapists = ['Therapist A', 'Therapist B', 'Therapist C'];
        $services = [
            ['id' => 1, 'name' => 'Swedish Massage', 'duration' => '1 hr', 'happy_hour_price' => 300, 'regular_price' => 350],
            ['id' => 2, 'name' => 'Therapeutic Massage', 'duration' => '1.5 hr', 'happy_hour_price' => null, 'regular_price' => 500],
            ['id' => 3, 'name' => 'Foot Massage', 'duration' => '1 hr', 'happy_hour_price' => null, 'regular_price' => 400],
            ['id' => 4, 'name' => 'Combo (Swedish + 1 add-on)', 'duration' => '1 hr', 'happy_hour_price' => 400, 'regular_price' => null],
        ];
        $addons = [
            ['id' => 'ventosa', 'name' => 'Ventosa', 'price' => 150],
            ['id' => 'hot-stone', 'name' => 'Hot Stone', 'price' => 150],
            ['id' => 'ear-candle', 'name' => 'Ear Candle', 'price' => 150],
            ['id' => '30-minute-massage', 'name' => '30 Minutes Massage', 'price' => 200],
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Point of Sale</h1>
            <p class="mt-1 text-sm text-ink/65">Create a spa service order and process payment.</p>
        </div>
        <div id="happy-hour-indicator" class="inline-flex items-center gap-2 self-start rounded-full border border-gold/40 bg-gold/10 px-4 py-2 text-xs font-medium text-maroon sm:self-auto" aria-live="polite">
            <span class="h-2 w-2 rounded-full bg-gold" aria-hidden="true"></span>
            <span>Happy Hour: 11:00 AM - 4:00 PM</span>
        </div>
    </div>

    <div class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.8fr)]">
        <div class="pos-left-column min-w-0 space-y-5">
            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="transaction-details-heading">
                <h2 id="transaction-details-heading" class="mb-4 text-base font-semibold text-ink">Transaction Details</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="customer-search" class="mb-2 block text-sm font-medium text-ink">Customer</label>
                        <div class="space-y-2">
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                <input id="customer-search" type="search" placeholder="Search customer..." class="h-10 w-full rounded-lg border border-maroon/15 bg-white pl-9 pr-3 text-sm outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                            </div>
                            <select id="customer-select" class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer['id'] }}" data-phone="{{ $customer['phone'] }}" {{ $customer['id'] === 1 ? 'selected' : '' }}>
                                        {{ $customer['name'] }} &middot; {{ $customer['phone'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3 flex items-center gap-2 rounded-lg bg-cream/70 px-3 py-2.5">
                            <span id="customer-initials" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gold/20 text-xs font-semibold text-maroon">MS</span>
                            <div class="min-w-0">
                                <p id="selected-customer-name" class="truncate text-sm font-medium text-ink">Maria Santos</p>
                                <p id="selected-customer-phone" class="text-xs text-ink/55">0917 123 4567</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="therapist-select" class="mb-2 block text-sm font-medium text-ink">Therapist</label>
                        <select id="therapist-select" class="h-10 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            <option value="">Select a therapist</option>
                            @foreach ($therapists as $therapist)
                                <option value="{{ $therapist }}">{{ $therapist }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="service-picker-heading">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 id="service-picker-heading" class="text-base font-semibold text-ink">Select a Service</h2>
                        <p class="mt-1 text-xs text-ink/55">Choose one treatment for this transaction.</p>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21c-4-3-7-6-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4-3 7-7 10Z"/><path d="M12 9c-2-3-2-5 0-7 2 2 2 4 0 7Z"/></svg>
                </div>
                <div id="service-options" class="grid gap-3 sm:grid-cols-2">
                    @foreach ($services as $service)
                        <button
                            type="button"
                            data-pos-service
                            data-service-id="{{ $service['id'] }}"
                            data-service-name="{{ $service['name'] }}"
                            data-duration="{{ $service['duration'] }}"
                            data-happy-price="{{ $service['happy_hour_price'] ?? '' }}"
                            data-regular-price="{{ $service['regular_price'] ?? '' }}"
                            aria-pressed="{{ $service['id'] === 1 ? 'true' : 'false' }}"
                            class="pos-service-card flex min-h-28 flex-col justify-between rounded-xl border border-maroon/15 bg-white p-4 text-left transition hover:border-gold hover:bg-gold/5 focus:outline-none focus:ring-2 focus:ring-gold {{ $service['id'] === 1 ? 'border-gold bg-gold/10 ring-1 ring-gold/40' : '' }}">
                            <span class="flex items-start justify-between gap-2">
                                <span>
                                    <span class="block text-sm font-semibold text-ink">{{ $service['name'] }}</span>
                                    <span class="mt-1 block text-xs text-ink/55">{{ $service['duration'] }}</span>
                                </span>
                                <svg class="service-check hidden h-5 w-5 shrink-0 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <span class="mt-3 flex items-baseline justify-between gap-2">
                                <span data-service-price class="text-base font-semibold text-maroon">Checking price...</span>
                                <span data-price-type class="text-[10px] font-medium uppercase tracking-wide text-ink/45"></span>
                            </span>
                        </button>
                    @endforeach
                </div>
                <p id="service-price-note" class="mt-3 text-xs text-ink/55"></p>
            </section>

            <section class="rounded-xl border border-maroon/10 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="addons-picker-heading">
                <div class="mb-4">
                    <h2 id="addons-picker-heading" class="text-base font-semibold text-ink">Add-ons</h2>
                    <p class="mt-1 text-xs text-ink/55">Select any extras to add to this service.</p>
                </div>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($addons as $addon)
                        <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg border border-maroon/10 px-3 py-3 transition hover:border-gold hover:bg-gold/5">
                            <span class="flex min-w-0 items-center gap-3">
                                <input type="checkbox" data-pos-addon value="{{ $addon['id'] }}" data-addon-name="{{ $addon['name'] }}" data-addon-price="{{ $addon['price'] }}" class="h-4 w-4 shrink-0 rounded border-maroon/25 accent-maroon focus:ring-gold">
                                <span class="truncate text-sm font-medium text-ink">{{ $addon['name'] }}</span>
                            </span>
                            <span class="whitespace-nowrap text-sm font-medium text-maroon">₱{{ number_format($addon['price']) }}</span>
                        </label>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="min-w-0">
            <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="order-summary-heading">
                <div class="flex items-center justify-between gap-3 border-b border-maroon/10 bg-cream/60 px-5 py-4">
                    <div>
                        <h2 id="order-summary-heading" class="font-display text-3xl text-maroon">Order Summary</h2>
                        <p class="mt-0.5 text-xs text-ink/55">Review items before payment</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gold/20 text-maroon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                    </span>
                </div>
                <div class="space-y-4 p-5">
                    <div class="border-b border-maroon/10 pb-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-ink/50">Service</h3>
                        <div class="mt-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p id="summary-service-name" class="text-sm font-semibold text-ink">Swedish Massage</p>
                                <p id="summary-service-duration" class="mt-1 text-xs text-ink/55">1 hr</p>
                            </div>
                            <p id="summary-service-price" class="whitespace-nowrap text-sm font-semibold text-ink">₱350</p>
                        </div>
                        <div id="summary-addons" class="mt-3 space-y-2"></div>
                        <p id="no-addons-message" class="mt-2 text-xs text-ink/45">No add-ons selected</p>
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-3 text-ink/65">
                            <dt>Subtotal</dt>
                            <dd id="summary-subtotal" class="font-medium text-ink">₱350</dd>
                        </div>
                        <div class="flex justify-between gap-3 border-t border-maroon/10 pt-3 text-base font-semibold text-maroon">
                            <dt>Total Due</dt>
                            <dd id="summary-total">₱350</dd>
                        </div>
                    </dl>

                    <div class="space-y-3 border-t border-maroon/10 pt-4">
                        <div>
                            <label for="payment-method" class="mb-1.5 block text-sm font-medium text-ink">Payment Method</label>
                            <select id="payment-method" class="h-11 w-full rounded-lg border border-maroon/15 bg-white px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                                <option value="Cash">Cash</option>
                                <option value="E-cash">E-cash</option>
                            </select>
                        </div>
                        <div>
                            <label for="amount-paid" class="mb-1.5 block text-sm font-medium text-ink">Amount Paid</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink/50">₱</span>
                                <input id="amount-paid" type="number" min="0" step="0.01" value="0" inputmode="decimal" class="h-11 w-full rounded-lg border border-maroon/15 bg-white pl-8 pr-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-cream/70 px-3 py-3">
                            <span class="text-sm font-medium text-ink">Change</span>
                            <span id="change-due" class="text-sm font-semibold text-maroon">—</span>
                        </div>
                        <p id="payment-warning" class="rounded-lg bg-amber-50 px-3 py-2.5 text-xs font-medium text-amber-800" role="status" aria-live="polite">Enter the amount paid to calculate change.</p>
                        <p id="payment-message" class="hidden rounded-lg bg-emerald-50 px-3 py-2.5 text-xs font-medium text-emerald-800" role="status" aria-live="polite"></p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <button id="process-payment" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-maroon px-4 py-3 text-sm font-semibold text-white transition hover:bg-maroon/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg>
                            Process Payment
                        </button>
                        <button id="print-receipt" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-maroon/15 bg-white px-4 py-3 text-sm font-semibold text-maroon transition hover:border-gold hover:bg-gold/5 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>
                            Print Receipt
                        </button>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <script>
        const currencyFormatter = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
        const serviceCards = [...document.querySelectorAll('[data-pos-service]')];
        const addonInputs = [...document.querySelectorAll('[data-pos-addon]')];
        const amountPaidInput = document.getElementById('amount-paid');
        const paymentWarning = document.getElementById('payment-warning');
        const paymentMessage = document.getElementById('payment-message');
        let isHappyHour = false;
        let selectedService = null;
        let servicePrice = 0;

        function formatCurrency(amount) {
            return currencyFormatter.format(amount);
        }

        function getCurrentServicePrice(card) {
            const happyPrice = card.dataset.happyPrice === '' ? null : Number(card.dataset.happyPrice);
            const regularPrice = card.dataset.regularPrice === '' ? null : Number(card.dataset.regularPrice);

            if (isHappyHour) {
                return {
                    price: happyPrice ?? regularPrice,
                    label: happyPrice === null ? 'Regular price' : 'Happy Hour',
                };
            }

            return {
                price: regularPrice,
                label: 'Regular price',
            };
        }

        function updateHappyHourPricing() {
            const now = new Date();
            const minuteOfDay = now.getHours() * 60 + now.getMinutes();

            isHappyHour = minuteOfDay >= 11 * 60 && minuteOfDay <= 16 * 60;
            document.getElementById('happy-hour-indicator').innerHTML = `
                <span class="h-2 w-2 rounded-full ${isHappyHour ? 'bg-emerald-500' : 'bg-gold'}" aria-hidden="true"></span>
                <span>${isHappyHour ? 'Happy Hour is active' : 'Regular pricing'} · Happy Hour: 11:00 AM - 4:00 PM</span>
            `;

            serviceCards.forEach((card) => {
                const { price, label } = getCurrentServicePrice(card);
                const priceLabel = card.querySelector('[data-service-price]');
                const priceType = card.querySelector('[data-price-type]');
                const isUnavailable = price === null;

                card.disabled = isUnavailable;
                card.classList.toggle('cursor-not-allowed', isUnavailable);
                card.classList.toggle('opacity-55', isUnavailable);
                priceLabel.textContent = isUnavailable
                    ? `Happy Hour only · ${formatCurrency(Number(card.dataset.happyPrice))}`
                    : formatCurrency(price);
                priceType.textContent = isUnavailable ? '' : label;
                card.dataset.currentPrice = isUnavailable ? '' : String(price);
            });

            document.getElementById('service-price-note').textContent = isHappyHour
                ? 'Happy Hour prices are applied to eligible services. Services without a Happy Hour rate use their regular price.'
                : 'Regular prices are shown. The Combo service is available during Happy Hour only.';

            const currentCard = serviceCards.find((card) => card.dataset.serviceId === selectedService?.id);

            if (currentCard?.disabled) {
                selectService(serviceCards.find((card) => !card.disabled) ?? null);
            } else if (currentCard) {
                servicePrice = Number(currentCard.dataset.currentPrice);
                updateOrderSummary();
            }
        }

        function selectService(card) {
            selectedService = card
                ? {
                    id: card.dataset.serviceId,
                    name: card.dataset.serviceName,
                    duration: card.dataset.duration,
                }
                : null;
            servicePrice = card ? Number(card.dataset.currentPrice) : 0;

            serviceCards.forEach((serviceCard) => {
                const isSelected = serviceCard === card;

                serviceCard.setAttribute('aria-pressed', String(isSelected));
                serviceCard.classList.toggle('border-gold', isSelected);
                serviceCard.classList.toggle('bg-gold/10', isSelected);
                serviceCard.classList.toggle('ring-1', isSelected);
                serviceCard.classList.toggle('ring-gold/40', isSelected);
                serviceCard.querySelector('.service-check').classList.toggle('hidden', !isSelected);
            });

            updateOrderSummary();
        }

        function getSelectedAddons() {
            return addonInputs
                .filter((input) => input.checked)
                .map((input) => ({
                    name: input.dataset.addonName,
                    price: Number(input.dataset.addonPrice),
                }));
        }

        function updateOrderSummary() {
            const selectedAddons = getSelectedAddons();
            const addonsTotal = selectedAddons.reduce((total, addon) => total + addon.price, 0);
            const total = servicePrice + addonsTotal;
            const amountPaid = Number(amountPaidInput.value) || 0;
            const summaryAddons = document.getElementById('summary-addons');

            document.getElementById('summary-service-name').textContent = selectedService?.name ?? 'Select a service';
            document.getElementById('summary-service-duration').textContent = selectedService?.duration ?? '';
            document.getElementById('summary-service-price').textContent = selectedService ? formatCurrency(servicePrice) : '—';
            document.getElementById('no-addons-message').classList.toggle('hidden', selectedAddons.length > 0);
            summaryAddons.replaceChildren();

            selectedAddons.forEach((addon) => {
                const line = document.createElement('div');
                line.className = 'flex justify-between gap-3 text-xs text-ink/65';
                const name = document.createElement('span');
                name.textContent = addon.name;
                const price = document.createElement('span');
                price.className = 'whitespace-nowrap font-medium text-ink';
                price.textContent = formatCurrency(addon.price);
                line.append(name, price);
                summaryAddons.appendChild(line);
            });

            document.getElementById('summary-subtotal').textContent = formatCurrency(total);
            document.getElementById('summary-total').textContent = formatCurrency(total);

            if (!selectedService) {
                document.getElementById('change-due').textContent = '—';
                paymentWarning.textContent = 'Select a service before processing payment.';
                paymentWarning.classList.remove('hidden');
                document.getElementById('process-payment').disabled = true;
                return;
            }

            if (amountPaid < total) {
                const shortAmount = total - amountPaid;

                document.getElementById('change-due').textContent = '—';
                paymentWarning.textContent = `Payment is short by ${formatCurrency(shortAmount)}.`;
                paymentWarning.classList.remove('hidden');
                document.getElementById('process-payment').disabled = true;
            } else {
                document.getElementById('change-due').textContent = formatCurrency(amountPaid - total);
                paymentWarning.classList.add('hidden');
                document.getElementById('process-payment').disabled = false;
            }

            paymentMessage.classList.add('hidden');
            paymentMessage.textContent = '';
        }

        document.getElementById('customer-search').addEventListener('input', (event) => {
            const searchTerm = event.target.value.trim().toLowerCase();
            const customerSelect = document.getElementById('customer-select');

            [...customerSelect.options].forEach((option) => {
                const matches = option.textContent.toLowerCase().includes(searchTerm);

                option.hidden = !matches && option.value !== customerSelect.value;
            });
        });

        document.getElementById('customer-select').addEventListener('change', (event) => {
            const selectedOption = event.target.selectedOptions[0];
            const customerName = selectedOption.textContent.split('·')[0].trim();
            const initials = customerName.split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase();

            document.getElementById('selected-customer-name').textContent = customerName;
            document.getElementById('selected-customer-phone').textContent = selectedOption.dataset.phone;
            document.getElementById('customer-initials').textContent = initials;
            document.getElementById('customer-search').value = '';
            [...event.target.options].forEach((option) => {
                option.hidden = false;
            });
        });

        serviceCards.forEach((card) => {
            card.addEventListener('click', () => selectService(card));
        });
        addonInputs.forEach((input) => input.addEventListener('change', updateOrderSummary));
        amountPaidInput.addEventListener('input', updateOrderSummary);
        document.getElementById('process-payment').addEventListener('click', () => {
            paymentMessage.textContent = `Payment recorded for this demo using ${document.getElementById('payment-method').value}.`;
            paymentMessage.classList.remove('hidden');
        });
        document.getElementById('print-receipt').addEventListener('click', () => window.print());

        updateHappyHourPricing();
        selectService(serviceCards.find((card) => card.dataset.serviceId === '1' && !card.disabled) ?? null);
    </script>
@endsection
