@extends('layouts.app')

@section('title', 'Transaction History')
@section('breadcrumb', 'Transaction History')

@section('content')
    @php
        $transactions = [
            ['number' => 'TXN-2026-001', 'date' => '2026-11-20', 'display_date' => 'Nov 20, 2026', 'customer' => 'Maria Santos', 'service' => 'Swedish Massage', 'therapist' => 'Ana Reyes', 'addons' => 'Ventosa', 'total' => 500, 'paid' => 500, 'change' => 0, 'method' => 'Cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-002', 'date' => '2026-11-20', 'display_date' => 'Nov 20, 2026', 'customer' => 'John Rivera', 'service' => 'Foot Massage', 'therapist' => 'Liza Cruz', 'addons' => 'Hot Stone', 'total' => 550, 'paid' => 600, 'change' => 50, 'method' => 'Cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-003', 'date' => '2026-11-19', 'display_date' => 'Nov 19, 2026', 'customer' => 'Angela Cruz', 'service' => 'Therapeutic Massage', 'therapist' => 'Maya Flores', 'addons' => 'Ear Candle', 'total' => 650, 'paid' => 650, 'change' => 0, 'method' => 'E-cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-004', 'date' => '2026-11-19', 'display_date' => 'Nov 19, 2026', 'customer' => 'Robert Lim', 'service' => 'Combo', 'therapist' => 'Ana Reyes', 'addons' => '30 Minutes Massage', 'total' => 600, 'paid' => 0, 'change' => 0, 'method' => 'E-cash', 'status' => 'Unpaid'],
            ['number' => 'TXN-2026-005', 'date' => '2026-11-18', 'display_date' => 'Nov 18, 2026', 'customer' => 'Sophia Mendoza', 'service' => 'Swedish Massage', 'therapist' => 'Nina Santos', 'addons' => 'Hot Stone, Ear Candle', 'total' => 800, 'paid' => 800, 'change' => 0, 'method' => 'Cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-006', 'date' => '2026-11-18', 'display_date' => 'Nov 18, 2026', 'customer' => 'Carlos Garcia', 'service' => 'Foot Massage', 'therapist' => 'Liza Cruz', 'addons' => 'Ventosa', 'total' => 550, 'paid' => 550, 'change' => 0, 'method' => 'E-cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-007', 'date' => '2026-11-17', 'display_date' => 'Nov 17, 2026', 'customer' => 'Patricia Gomez', 'service' => 'Combo', 'therapist' => 'Maya Flores', 'addons' => 'Hot Stone', 'total' => 550, 'paid' => 0, 'change' => 0, 'method' => 'Cash', 'status' => 'Unpaid'],
            ['number' => 'TXN-2026-008', 'date' => '2026-11-16', 'display_date' => 'Nov 16, 2026', 'customer' => 'Daniel Torres', 'service' => 'Therapeutic Massage', 'therapist' => 'Ana Reyes', 'addons' => '30 Minutes Massage', 'total' => 700, 'paid' => 1000, 'change' => 300, 'method' => 'Cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-009', 'date' => '2026-11-15', 'display_date' => 'Nov 15, 2026', 'customer' => 'Isabel Ramos', 'service' => 'Swedish Massage', 'therapist' => 'Nina Santos', 'addons' => 'Ventosa, Ear Candle', 'total' => 650, 'paid' => 650, 'change' => 0, 'method' => 'E-cash', 'status' => 'Paid'],
            ['number' => 'TXN-2026-010', 'date' => '2026-11-14', 'display_date' => 'Nov 14, 2026', 'customer' => 'Miguel Bautista', 'service' => 'Foot Massage', 'therapist' => 'Liza Cruz', 'addons' => '30 Minutes Massage', 'total' => 600, 'paid' => 600, 'change' => 0, 'method' => 'E-cash', 'status' => 'Paid'],
        ];
    @endphp

    <div class="mb-7">
        <h1 class="font-display text-5xl leading-tight text-maroon sm:text-6xl">Transaction History</h1>
        <p class="mt-1 text-sm text-ink/65">Review completed and unpaid spa transactions.</p>
    </div>

    <section class="mb-5 rounded-xl border border-maroon/10 bg-white p-4 shadow-sm" aria-label="Transaction filters">
        <div class="grid gap-3 lg:grid-cols-[minmax(220px,1.5fr)_repeat(2,minmax(150px,1fr))_minmax(150px,.8fr)_minmax(150px,.8fr)]">
            <div>
                <label for="transaction-search" class="mb-1.5 block text-xs font-semibold text-ink/70">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="transaction-search" type="search" placeholder="Customer or transaction no." class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 pl-9 pr-3 text-sm outline-none placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                </div>
            </div>
            <div>
                <label for="date-from" class="mb-1.5 block text-xs font-semibold text-ink/70">Date from</label>
                <input id="date-from" type="date" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
            </div>
            <div>
                <label for="date-to" class="mb-1.5 block text-xs font-semibold text-ink/70">Date to</label>
                <input id="date-to" type="date" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
            </div>
            <div>
                <label for="payment-method" class="mb-1.5 block text-xs font-semibold text-ink/70">Payment Method</label>
                <select id="payment-method" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    <option>All</option>
                    <option>Cash</option>
                    <option>E-cash</option>
                </select>
            </div>
            <div>
                <label for="payment-status" class="mb-1.5 block text-xs font-semibold text-ink/70">Payment Status</label>
                <select id="payment-status" class="h-11 w-full rounded-lg border border-maroon/15 bg-cream/30 px-3 text-sm text-ink outline-none focus:border-gold focus:ring-2 focus:ring-gold/25">
                    <option>All</option>
                    <option>Paid</option>
                    <option>Unpaid</option>
                </select>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-maroon/10 bg-white shadow-sm" aria-labelledby="transactions-heading">
        <div class="border-b border-maroon/10 px-5 py-4">
            <h2 id="transactions-heading" class="text-base font-semibold text-ink">Transactions</h2>
            <p id="transaction-count" class="mt-1 text-xs text-ink/55">{{ count($transactions) }} transactions</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] text-left text-sm">
                <thead class="bg-cream/75 text-xs text-ink/70">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Transaction No.</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Date</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Customer</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Service</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Therapist</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Add-ons</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Total</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Payment Method</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body" class="divide-y divide-maroon/5">
                    @foreach ($transactions as $transaction)
                        <tr
                            data-transaction-row
                            data-number="{{ $transaction['number'] }}"
                            data-date="{{ $transaction['date'] }}"
                            data-customer="{{ $transaction['customer'] }}"
                            data-method="{{ $transaction['method'] }}"
                            data-status="{{ $transaction['status'] }}"
                            class="transition hover:bg-cream/45">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-maroon">{{ $transaction['number'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $transaction['display_date'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-ink">{{ $transaction['customer'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $transaction['service'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $transaction['therapist'] }}</td>
                            <td class="px-5 py-4 text-ink/75">{{ $transaction['addons'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-maroon">₱{{ number_format($transaction['total']) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-ink/75">{{ $transaction['method'] }}</td>
                            <td class="px-5 py-4">
                                @if ($transaction['status'] === 'Paid')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Paid</span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    data-view-receipt
                                    data-number="{{ $transaction['number'] }}"
                                    data-date="{{ $transaction['display_date'] }}"
                                    data-customer="{{ $transaction['customer'] }}"
                                    data-service="{{ $transaction['service'] }}"
                                    data-addons="{{ $transaction['addons'] }}"
                                    data-total="{{ $transaction['total'] }}"
                                    data-paid="{{ $transaction['paid'] }}"
                                    data-change="{{ $transaction['change'] }}"
                                    class="whitespace-nowrap rounded-lg border border-maroon/15 px-3 py-2 text-xs font-semibold text-maroon transition hover:border-gold hover:bg-gold/10 focus:outline-none focus:ring-2 focus:ring-gold">View Receipt</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="no-transactions" class="hidden px-5 py-10 text-center text-sm text-ink/60">No transactions match your filters.</p>
        </div>
        <div class="flex flex-col items-center justify-between gap-3 border-t border-maroon/10 px-5 py-4 sm:flex-row">
            <p id="pagination-summary" class="text-xs text-ink/55">Showing 1–10 of 10 transactions</p>
            <nav class="flex items-center gap-1" aria-label="Transaction pagination">
                <button type="button" disabled class="flex h-9 w-9 items-center justify-center rounded-lg border border-maroon/10 text-ink/35" aria-label="Previous page">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" aria-current="page" class="h-9 min-w-9 rounded-lg bg-maroon px-3 text-xs font-semibold text-white">1</button>
                <button type="button" disabled class="flex h-9 w-9 items-center justify-center rounded-lg border border-maroon/10 text-ink/35" aria-label="Next page">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </nav>
        </div>
    </section>

    <div id="receipt-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-ink/60 p-4" aria-hidden="true">
        <section class="my-auto w-full max-w-md rounded-2xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="receipt-title">
            <div id="receipt-print-area" class="p-6 sm:p-8">
                <div class="text-center">
                    <h2 id="receipt-title" class="font-display text-4xl text-maroon">Chillax Spa</h2>
                    <p class="mt-1 text-xs text-ink/55">Relax · Refresh · Rejuvenate</p>
                </div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-ink/55">Transaction</span><strong id="receipt-number" class="text-right text-ink"></strong></div>
                    <div class="flex justify-between gap-4"><span class="text-ink/55">Date</span><strong id="receipt-date" class="text-right text-ink"></strong></div>
                    <div class="flex justify-between gap-4"><span class="text-ink/55">Customer</span><strong id="receipt-customer" class="text-right text-ink"></strong></div>
                </div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><span id="receipt-service" class="text-ink"></span><strong id="receipt-service-total" class="shrink-0 text-ink"></strong></div>
                    <div id="receipt-addons-row" class="flex justify-between gap-4 text-ink/70"><span>Add-ons</span><strong id="receipt-addons" class="text-right"></strong></div>
                    <div class="flex justify-between gap-4 border-t border-maroon/10 pt-3 text-base"><strong class="text-ink">Total</strong><strong id="receipt-total" class="text-maroon"></strong></div>
                </div>
                <div class="my-5 border-t border-dashed border-maroon/25"></div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-ink/55">Amount Paid</span><strong id="receipt-paid" class="text-ink"></strong></div>
                    <div class="flex justify-between gap-4"><span class="text-ink/55">Change</span><strong id="receipt-change" class="text-ink"></strong></div>
                </div>
                <p class="mt-8 text-center text-xs text-ink/50">Thank you for choosing Chillax Spa!</p>
            </div>
            <div id="receipt-actions" class="flex justify-end gap-3 border-t border-maroon/10 p-4">
                <button id="close-receipt-modal" type="button" class="min-h-10 rounded-lg border border-maroon/15 px-4 text-sm font-medium text-ink transition hover:bg-maroon/5">Close</button>
                <button id="print-receipt" type="button" class="min-h-10 rounded-lg bg-gold px-4 text-sm font-semibold text-maroon transition hover:bg-gold/90">
                    Print
                </button>
            </div>
        </section>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt-modal, #receipt-modal * { visibility: visible; }
            #receipt-modal { position: fixed; inset: 0; display: block !important; background: white !important; padding: 0 !important; }
            #receipt-modal section { max-width: none; box-shadow: none; border: 0; }
            #receipt-actions { display: none; }
        }
    </style>

    <script>
        const receiptModal = document.getElementById('receipt-modal');
        const searchInput = document.getElementById('transaction-search');
        const dateFromInput = document.getElementById('date-from');
        const dateToInput = document.getElementById('date-to');
        const methodInput = document.getElementById('payment-method');
        const statusInput = document.getElementById('payment-status');
        const rows = [...document.querySelectorAll('[data-transaction-row]')];

        function formatPeso(value) {
            return `₱${Number(value).toLocaleString('en-PH')}`;
        }

        function setReceiptModalOpen(isOpen) {
            receiptModal.classList.toggle('hidden', !isOpen);
            receiptModal.classList.toggle('flex', isOpen);
            receiptModal.setAttribute('aria-hidden', String(!isOpen));
            if (isOpen) {
                document.getElementById('close-receipt-modal').focus();
            }
        }

        document.querySelectorAll('[data-view-receipt]').forEach((button) => {
            button.addEventListener('click', () => {
                const data = button.dataset;
                document.getElementById('receipt-number').textContent = data.number;
                document.getElementById('receipt-date').textContent = data.date;
                document.getElementById('receipt-customer').textContent = data.customer;
                document.getElementById('receipt-service').textContent = data.service;
                document.getElementById('receipt-service-total').textContent = formatPeso(data.total);
                document.getElementById('receipt-addons').textContent = data.addons;
                document.getElementById('receipt-total').textContent = formatPeso(data.total);
                document.getElementById('receipt-paid').textContent = formatPeso(data.paid);
                document.getElementById('receipt-change').textContent = formatPeso(data.change);
                setReceiptModalOpen(true);
            });
        });

        document.getElementById('close-receipt-modal').addEventListener('click', () => setReceiptModalOpen(false));
        document.getElementById('print-receipt').addEventListener('click', () => window.print());
        receiptModal.addEventListener('click', (event) => {
            if (event.target === receiptModal) setReceiptModalOpen(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setReceiptModalOpen(false);
        });

        function filterTransactions() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;
            rows.forEach((row) => {
                const matchesSearch = !query || row.dataset.number.toLowerCase().includes(query) || row.dataset.customer.toLowerCase().includes(query);
                const matchesFrom = !dateFromInput.value || row.dataset.date >= dateFromInput.value;
                const matchesTo = !dateToInput.value || row.dataset.date <= dateToInput.value;
                const matchesMethod = methodInput.value === 'All' || row.dataset.method === methodInput.value;
                const matchesStatus = statusInput.value === 'All' || row.dataset.status === statusInput.value;
                const isVisible = matchesSearch && matchesFrom && matchesTo && matchesMethod && matchesStatus;
                row.classList.toggle('hidden', !isVisible);
                if (isVisible) visibleCount += 1;
            });
            document.getElementById('no-transactions').classList.toggle('hidden', visibleCount !== 0);
            document.getElementById('transaction-count').textContent = `${visibleCount} transaction${visibleCount === 1 ? '' : 's'}`;
            document.getElementById('pagination-summary').textContent = visibleCount === 0
                ? 'Showing 0 transactions'
                : `Showing 1–${visibleCount} of ${visibleCount} transactions`;
        }

        [searchInput, dateFromInput, dateToInput, methodInput, statusInput].forEach((control) => {
            control.addEventListener(control.tagName === 'INPUT' && control.type === 'search' ? 'input' : 'change', filterTransactions);
        });
    </script>
@endsection
