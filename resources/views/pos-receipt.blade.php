@extends('layouts.app')

@section('title', 'Receipt')
@section('breadcrumb', 'Payment Receipt')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-5 flex flex-wrap gap-3 receipt-actions">
        <button type="button" onclick="window.print()" class="rounded-lg bg-maroon px-5 py-3 text-sm font-semibold text-white">Print</button>
        <a href="{{ route('pos') }}" class="rounded-lg border border-maroon/20 bg-white px-5 py-3 text-sm font-semibold text-maroon">New Transaction</a>
        <a href="{{ route('appointments') }}" class="rounded-lg border border-maroon/20 bg-white px-5 py-3 text-sm font-semibold text-maroon">Back to Appointments</a>
    </div>
    <article class="receipt-ticket mx-auto rounded-xl border border-maroon/10 bg-white p-7 shadow-sm">
        <header class="border-b border-dashed border-maroon/30 pb-5 text-center">
            <h1 class="font-display text-5xl text-maroon">Chillax Spa</h1>
            <p class="mt-1 text-xs tracking-widest text-ink/60">RELAX · REFRESH · REJUVENATE</p>
            <p class="mt-5 text-sm font-semibold">Receipt {{ 'CS-'.str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p class="mt-1 text-xs text-ink/60">{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date)->timezone('Asia/Manila')->format('M j, Y · g:i A') }}</p>
        </header>
        <div class="space-y-1 border-b border-dashed border-maroon/30 py-4 text-sm"><p><span class="text-ink/55">Cashier:</span> {{ $transaction->user->name }}</p><p><span class="text-ink/55">Customer:</span> {{ $transaction->customer->customer_name }}</p><p><span class="text-ink/55">Payment:</span> {{ $transaction->payment_method === 'ecash' ? 'E-cash' : 'Cash' }}</p></div>
        <div class="space-y-4 border-b border-dashed border-maroon/30 py-4">
            @foreach ($transaction->details as $detail)
                <div class="text-sm">
                    <div class="flex justify-between gap-3 font-semibold"><span>{{ $detail->service->service_name }}</span><span>₱{{ number_format((float) $detail->service_price, 2) }}</span></div>
                    <p class="mt-1 text-xs text-ink/60">Therapist: {{ $detail->therapist->therapist_name }}</p>
                    @foreach ($detail->addons as $detailAddon)
                        <div class="mt-1 flex justify-between gap-3 pl-3 text-xs text-ink/65"><span>{{ $detailAddon->addon->addon_name }}</span><span>{{ (float) $detailAddon->addon_price === 0.0 ? 'Included' : '₱'.number_format((float) $detailAddon->addon_price, 2) }}</span></div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="space-y-2 py-4 text-sm"><div class="flex justify-between text-base font-bold text-maroon"><span>Total</span><span>₱{{ number_format((float) $transaction->total_amount, 2) }}</span></div><div class="flex justify-between"><span>Amount paid</span><span>₱{{ number_format((float) $transaction->amount_paid, 2) }}</span></div><div class="flex justify-between"><span>Change</span><span>₱{{ number_format((float) $transaction->change_amount, 2) }}</span></div></div>
        <footer class="border-t border-dashed border-maroon/30 pt-5 text-center text-xs text-ink/60">Thank you for choosing Chillax Spa.</footer>
    </article>
</div>
<style>
@media print {
    @page { size: 80mm auto; margin: 5mm; }
    body { background: #fff !important; }
    #sidebar, #sidebar-overlay, #app-header, .receipt-actions { display: none !important; }
    body > div, body main, .md\:pl-72 { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    main { max-width: none !important; }
    .receipt-ticket { width: 70mm !important; max-width: 70mm !important; margin: 0 auto !important; padding: 0 !important; border: 0 !important; border-radius: 0 !important; box-shadow: none !important; color: #111 !important; }
    .receipt-ticket * { color: #111 !important; }
}
</style>
@endsection
