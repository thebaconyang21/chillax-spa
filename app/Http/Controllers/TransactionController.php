<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'method' => ['nullable', 'in:all,cash,ecash'],
            'status' => ['nullable', 'in:all,paid,unpaid'],
        ]);
        $search = trim((string) ($validated['search'] ?? ''));
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $invalidRange = $from !== null && $to !== null && Carbon::parse($from)->gt(Carbon::parse($to));
        if ($invalidRange) {
            $request->session()->flash('error', 'The start date must not be after the end date.');
        }

        $query = Transaction::query()->with([
            'customer',
            'user',
            'details.service',
            'details.therapist',
            'details.addons.addon',
        ]);

        if ($search !== '') {
            $receiptSearch = preg_replace('/^CS-/i', '', $search);
            $receiptSearch = ltrim($receiptSearch, '0');
            $receiptId = $receiptSearch !== '' && ctype_digit($receiptSearch) ? (int) $receiptSearch : null;
            $query->where(function (Builder $builder) use ($search, $receiptId): void {
                $builder->whereHas('customer', fn (Builder $customerQuery) => $customerQuery->where('customer_name', 'like', '%'.$search.'%'));
                if ($receiptId !== null) {
                    $builder->orWhere('id', $receiptId);
                }
            });
        }

        if (! $invalidRange) {
            if ($from !== null) {
                $query->where('transaction_date', '>=', Carbon::parse($from, 'Asia/Manila')->startOfDay()->utc());
            }
            if ($to !== null) {
                $query->where('transaction_date', '<=', Carbon::parse($to, 'Asia/Manila')->endOfDay()->utc());
            }
        }
        if (($validated['method'] ?? 'all') !== 'all') {
            $query->where('payment_method', $validated['method']);
        }
        if (($validated['status'] ?? 'all') !== 'all') {
            $query->where('payment_status', $validated['status']);
        }

        $summaryQuery = clone $query;
        $transactionCount = (clone $summaryQuery)->count();
        $totalSales = (clone $summaryQuery)->where('payment_status', 'paid')->sum('total_amount');

        $transactions = $query->orderByDesc('transaction_date')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('transactions', [
            'transactions' => $transactions,
            'transactionCount' => $transactionCount,
            'totalSales' => (float) $totalSales,
            'filters' => [
                'search' => $search,
                'from' => $from,
                'to' => $to,
                'method' => $validated['method'] ?? 'all',
                'status' => $validated['status'] ?? 'all',
            ],
            'invalidRange' => $invalidRange,
        ]);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load([
            'customer',
            'user',
            'details.service',
            'details.therapist',
            'details.addons.addon',
        ]);
        $date = Carbon::parse($transaction->transaction_date)->timezone('Asia/Manila');

        return response()->json([
            'receipt_number' => $transaction->receipt_number,
            'date' => $date->format('M j, Y g:i A'),
            'cashier' => $transaction->user->name,
            'customer' => $transaction->customer->customer_name,
            'lines' => $transaction->details->map(fn ($detail): array => [
                'service' => $detail->service->service_name,
                'therapist' => $detail->therapist->therapist_name,
                'service_price' => (float) $detail->service_price,
                'addons' => $detail->addons->map(fn ($detailAddon): array => [
                    'name' => $detailAddon->addon->addon_name,
                    'price' => (float) $detailAddon->addon_price,
                    'included' => (float) $detailAddon->addon_price === 0.0,
                ])->values(),
            ])->values(),
            'total' => (float) $transaction->total_amount,
            'amount_paid' => (float) $transaction->amount_paid,
            'change' => (float) $transaction->change_amount,
            'method' => $transaction->payment_method,
            'status' => $transaction->payment_status,
        ]);
    }
}
