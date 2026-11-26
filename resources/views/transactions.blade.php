@extends('layouts.app')

@section('title', 'Transaction History')
@section('breadcrumb', 'Transaction History')

@section('content')
    <div class="mb-7">
        <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Transaction History</h1>
        <p class="mt-1 text-sm text-ink/65">Review completed and unpaid spa transactions.</p>
    </div>

    @if (session('error'))
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <section class="mb-5 rounded-xl border border-maroon/10 bg-white p-4 shadow-sm" aria-label="Transaction filters">
        <form method="GET" action="{{ route('transactions') }}" class="grid gap-3 lg:grid-cols-[minmax(220px,1.5fr)_repeat(2,minmax(150px,1fr))_minmax(150px,.8fr)_minmax(150px,.8fr)_auto]">
            <div>
                <label for="transaction-search" class="mb-1.5 block text-xs font-semibold text-ink/70">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="transaction-search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Customer or receipt no." class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 pl-9 pr-3 text-sm outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
            </div>
            <div><label for="date-from" class="mb-1.5 block text-xs font-semibold text-ink/70">Date from</label><input id="date-from" name="from" type="date" value="{{ $filters['from'] }}" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25"></div>
            <div><label for="date-to" class="mb-1.5 block text-xs font-semibold text-ink/70">Date to</label><input id="date-to" name="to" type="date" value="{{ $filters['to'] }}" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25"></div>
            <div><label for="payment-method" class="mb-1.5 block text-xs font-semibold text-ink/70">Payment Method</label><select id="payment-method" name="method" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25"><option value="all" @selected($filters['method'] === 'all')>All</option><option value="cash" @selected($filters['method'] === 'cash')>Cash</option><option value="ecash" @selected($filters['method'] === 'ecash')>E-cash</option></select></div>
            <div><label for="payment-status" class="mb-1.5 block text-xs font-semibold text-ink/70">Payment Status</label><select id="payment-status" name="status" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25"><option value="all" @selected($filters['status'] === 'all')>All</option><option value="paid" @selected($filters['status'] === 'paid')>Paid</option><option value="unpaid" @selected($filters['status'] === 'unpaid')>Unpaid</option></select></div>
            <div class="flex items-end gap-2"><button type="submit" class="h-11 rounded-lg bg-maroon px-4 text-sm font-semibold text-white hover:bg-maroon/90">Filter</button><a href="{{ route('transactions') }}" class="inline-flex h-11 items-center whitespace-nowrap rounded-lg border border-maroon/15 px-3 text-sm font-medium text-maroon hover:bg-gold/10">Clear filters</a></div>
        </form>
    </section>

    <section class="mb-5 grid gap-4 sm:grid-cols-2" aria-label="Transaction totals">
        <article class="rounded-xl border border-maroon/10 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-ink/55">Transactions</p><p class="mt-2 text-3xl font-bold text-maroon">{{ number_format($transactionCount) }}</p></article>
        <article class="rounded-xl border border-gold/40 bg-gold/10 p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-ink/55">Total Sales</p><p class="mt-2 text-3xl font-bold text-maroon">₱{{ number_format($totalSales, 2) }}</p><p class="mt-1 text-xs text-ink/55">Paid transactions in the filtered results</p></article>
    </section>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="transactions-heading">
        <div class="border-b border-maroon/10 px-5 py-4"><h2 id="transactions-heading" class="text-base font-semibold text-ink">Transactions</h2><p class="mt-1 text-xs text-ink/55">{{ number_format($transactionCount) }} transactions</p></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1260px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70"><tr>
                    <th class="px-5 py-3.5 font-semibold">Receipt No.</th><th class="px-5 py-3.5 font-semibold">Date and time</th><th class="px-5 py-3.5 font-semibold">Customer</th><th class="px-5 py-3.5 font-semibold">Service(s)</th><th class="px-5 py-3.5 font-semibold">Therapist(s)</th><th class="px-5 py-3.5 font-semibold">Add-ons</th><th class="px-5 py-3.5 text-right font-semibold">Total</th><th class="px-5 py-3.5 font-semibold">Payment Method</th><th class="px-5 py-3.5 font-semibold">Status</th><th class="px-5 py-3.5 text-right font-semibold">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-maroon/5">
                    @forelse ($transactions as $transaction)
                        @php
                            $transactionLines = $transaction->details->map(fn ($detail) => $detail->service->service_name);
                            $therapistNames = $transaction->details->map(fn ($detail) => $detail->therapist->therapist_name);
                            $addonNames = $transaction->details->flatMap(fn ($detail) => $detail->addons->map(fn ($detailAddon) => $detailAddon->addon->addon_name));
                        @endphp
                        <tr class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-maroon">{{ $transaction->receipt_number }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date)->timezone('Asia/Manila')->format('M j, Y g:i A') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ $transaction->customer->customer_name }}</td>
                            <td class="px-5 py-4 text-ink/75"><div class="space-y-1">@foreach ($transactionLines as $name)<div>{{ $name }}</div>@endforeach</div></td>
                            <td class="px-5 py-4 text-ink/75"><div class="space-y-1">@foreach ($therapistNames as $name)<div>{{ $name }}</div>@endforeach</div></td>
                            <td class="px-5 py-4 text-ink/75">{{ $addonNames->isEmpty() ? '-' : $addonNames->implode(', ') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-maroon">₱{{ number_format((float) $transaction->total_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $transaction->payment_method === 'ecash' ? 'E-cash' : 'Cash' }}</td>
                            <td class="px-5 py-4">@if ($transaction->payment_status === 'paid')<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Paid</span>@else<span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">Unpaid</span>@endif</td>
                            <td class="px-5 py-4 text-right"><button type="button" data-view-receipt data-url="{{ route('transactions.show', $transaction) }}" class="whitespace-nowrap rounded-lg border border-maroon/15 px-3 py-2 text-xs font-semibold text-maroon transition hover:border-gold hover:bg-gold/10 focus:outline-none focus:ring-2 focus:ring-gold">View Receipt</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-10 text-center text-sm text-ink/60">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-maroon/10 px-5 py-4 text-sm">{{ $transactions->onEachSide(1)->links() }}</div>
        @endif
    </section>

    <div id="receipt-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-md rounded-2xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="receipt-title">
            <div id="receipt-print-area" class="receipt-ticket p-6 sm:p-8">
                <div class="text-center"><h2 id="receipt-title" class="font-display text-4xl text-maroon">Chillax Spa</h2><p class="mt-1 text-xs text-ink/55">Relax · Refresh · Rejuvenate</p></div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div class="space-y-2 text-sm"><div class="flex justify-between gap-4"><span class="text-ink/55">Receipt</span><strong id="receipt-number" class="text-right text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Date</span><strong id="receipt-date" class="text-right text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Cashier</span><strong id="receipt-cashier" class="text-right text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Customer</span><strong id="receipt-customer" class="text-right text-ink"></strong></div></div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div id="receipt-lines" class="space-y-4 text-sm"></div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div class="space-y-2 text-sm"><div class="flex justify-between gap-4 text-base"><strong class="text-ink">Total</strong><strong id="receipt-total" class="text-maroon"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Amount Paid</span><strong id="receipt-paid" class="text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Change</span><strong id="receipt-change" class="text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Payment Method</span><strong id="receipt-method" class="text-ink"></strong></div><div class="flex justify-between gap-4"><span class="text-ink/55">Status</span><strong id="receipt-status" class="text-ink"></strong></div></div>
                <p class="mt-8 text-center text-xs text-ink/50">Thank you for choosing Chillax Spa!</p>
            </div>
            <div id="receipt-actions" class="flex justify-end gap-3 border-t border-maroon/10 p-4"><button id="close-receipt-modal" type="button" class="min-h-10 rounded-lg border border-maroon/15 px-4 text-sm font-medium text-ink transition hover:bg-maroon/5">Close</button><button id="print-receipt" type="button" class="min-h-10 rounded-lg bg-gold px-4 text-sm font-semibold text-maroon transition hover:bg-gold/90">Print</button></div>
        </section>
    </div>

    <style>
        @media print {
            @page { size: 80mm auto; margin: 5mm; }
            body * { visibility: hidden !important; }
            #receipt-modal, #receipt-modal * { visibility: visible !important; }
            #receipt-modal { position: fixed !important; inset: 0 !important; display: block !important; overflow: visible !important; background: white !important; padding: 0 !important; }
            #receipt-modal section { width: 70mm !important; max-width: 70mm !important; margin: 0 auto !important; box-shadow: none !important; border: 0 !important; }
            #receipt-print-area { padding: 0 !important; }
            #receipt-actions { display: none !important; }
        }
    </style>

    <script>
        const receiptModal = document.getElementById('receipt-modal');
        const money = value => '₱' + Number(value || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const setText = (id, value) => { document.getElementById(id).textContent = value ?? ''; };

        function setReceiptModalOpen(isOpen) {
            receiptModal.classList.toggle('hidden', !isOpen);
            receiptModal.classList.toggle('flex', isOpen);
            receiptModal.setAttribute('aria-hidden', String(!isOpen));
            if (isOpen) document.getElementById('close-receipt-modal').focus();
        }

        document.querySelectorAll('[data-view-receipt]').forEach(button => {
            button.addEventListener('click', async () => {
                button.disabled = true;
                try {
                    const response = await fetch(button.dataset.url, {headers: {'Accept': 'application/json'}});
                    if (!response.ok) throw new Error('Unable to load this receipt.');
                    const data = await response.json();
                    setText('receipt-number', data.receipt_number);
                    setText('receipt-date', data.date);
                    setText('receipt-cashier', data.cashier);
                    setText('receipt-customer', data.customer);
                    setText('receipt-total', money(data.total));
                    setText('receipt-paid', money(data.amount_paid));
                    setText('receipt-change', money(data.change));
                    setText('receipt-method', data.method === 'ecash' ? 'E-cash' : 'Cash');
                    setText('receipt-status', data.status === 'paid' ? 'Paid' : 'Unpaid');

                    const lineContainer = document.getElementById('receipt-lines');
                    lineContainer.replaceChildren();
                    data.lines.forEach(line => {
                        const article = document.createElement('div');
                        article.className = 'border-b border-dashed border-maroon/20 pb-3';
                        const serviceRow = document.createElement('div');
                        serviceRow.className = 'flex justify-between gap-3 font-semibold';
                        const serviceName = document.createElement('span'); serviceName.textContent = line.service;
                        const servicePrice = document.createElement('span'); servicePrice.textContent = money(line.service_price);
                        serviceRow.append(serviceName, servicePrice); article.append(serviceRow);
                        const therapist = document.createElement('p'); therapist.className = 'mt-1 text-xs text-ink/60'; therapist.textContent = 'Therapist: ' + line.therapist; article.append(therapist);
                        line.addons.forEach(addon => {
                            const addonRow = document.createElement('div'); addonRow.className = 'mt-1 flex justify-between gap-3 pl-3 text-xs text-ink/65';
                            const addonName = document.createElement('span'); addonName.textContent = addon.name;
                            const addonPrice = document.createElement('span'); addonPrice.textContent = addon.included ? 'Included' : money(addon.price);
                            addonRow.append(addonName, addonPrice); article.append(addonRow);
                        });
                        lineContainer.append(article);
                    });
                    setReceiptModalOpen(true);
                } catch (error) {
                    alert(error.message);
                } finally {
                    button.disabled = false;
                }
            });
        });

        document.getElementById('close-receipt-modal').addEventListener('click', () => setReceiptModalOpen(false));
        document.getElementById('print-receipt').addEventListener('click', () => window.print());
        receiptModal.addEventListener('click', event => { if (event.target === receiptModal) setReceiptModalOpen(false); });
        document.addEventListener('keydown', event => { if (event.key === 'Escape') setReceiptModalOpen(false); });
    </script>
@endsection
