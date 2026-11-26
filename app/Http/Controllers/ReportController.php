<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Services\ReportRange;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $range = ReportRange::make($request->query('period'), $request->query('date'));
        $summary = $this->summary($range->utcStart(), $range->utcEnd());
        $previous = $this->summary($range->utcPreviousStart(), $range->utcPreviousEnd());
        $chart = $this->chart($range);
        $topServices = $this->topServices($range);
        $therapistCommissions = $this->therapistCommissions($range);
        $paymentMethods = $this->paymentMethods($range);
        $previousSales = (float) $previous->total_sales;
        $currentSales = (float) $summary->total_sales;

        return view('reports', [
            'range' => $range,
            'summary' => [
                'sales' => round($currentSales, 2),
                'transactions' => (int) $summary->total_transactions,
                'commission' => round((float) $summary->total_commission, 2),
                'net_income' => round($currentSales - (float) $summary->total_commission, 2),
                'average_sale' => $summary->total_transactions > 0 ? round($currentSales / (int) $summary->total_transactions, 2) : 0.0,
            ],
            'comparison' => [
                'percent' => $previousSales > 0 ? round((($currentSales - $previousSales) / $previousSales) * 100, 1) : null,
                'direction' => $currentSales >= $previousSales ? 'up' : 'down',
            ],
            'chartLabels' => $chart['labels'],
            'chartSales' => $chart['sales'],
            'chartCommissions' => $chart['commissions'],
            'topServices' => $topServices,
            'therapistCommissions' => $therapistCommissions,
            'paymentMethods' => $paymentMethods,
            'hasSales' => $currentSales > 0,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $range = ReportRange::make($request->query('period'), $request->query('date'));
        $summary = $this->summary($range->utcStart(), $range->utcEnd());
        $chart = $this->chart($range);
        $topServices = $this->topServices($range);
        $therapists = $this->therapistCommissions($range);
        $filename = 'chillax-spa-report-'.$range->period.'-'.$range->start->toDateString().'.csv';

        return response()->streamDownload(function () use ($range, $summary, $chart, $topServices, $therapists): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Chillax Spa Report', $range->label]);
            fputcsv($output, []);
            fputcsv($output, ['Summary']);
            fputcsv($output, ['Total Sales', $this->formatMoney((float) $summary->total_sales)]);
            fputcsv($output, ['Total Transactions', (int) $summary->total_transactions]);
            fputcsv($output, ['Total Therapist Commission', $this->formatMoney((float) $summary->total_commission)]);
            fputcsv($output, ['Net Income for the Spa', $this->formatMoney((float) $summary->total_sales - (float) $summary->total_commission)]);
            fputcsv($output, ['Average Sale', $this->formatMoney($summary->total_transactions > 0 ? (float) $summary->total_sales / (int) $summary->total_transactions : 0)]);
            fputcsv($output, []);
            fputcsv($output, [$this->chartSectionTitle($range), 'Sales', 'Therapist Commission']);
            foreach ($chart['labels'] as $index => $label) {
                fputcsv($output, [$label, $this->formatMoney((float) $chart['sales'][$index]), $this->formatMoney((float) $chart['commissions'][$index])]);
            }
            fputcsv($output, []);
            fputcsv($output, ['Top Services']);
            fputcsv($output, ['Service Name', 'Times Availed', 'Total Sales']);
            foreach ($topServices as $service) {
                fputcsv($output, [$service->service_name, (int) $service->times_availed, $this->formatMoney((float) $service->total_sales)]);
            }
            fputcsv($output, []);
            fputcsv($output, ['Therapist Commission']);
            fputcsv($output, ['Therapist', 'Services Done', 'Sales', 'Commission']);
            foreach ($therapists as $therapist) {
                fputcsv($output, [$therapist->therapist_name, (int) $therapist->services_done, $this->formatMoney((float) $therapist->sales), $this->formatMoney((float) $therapist->commission)]);
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function summary(CarbonImmutable $start, CarbonImmutable $end): object
    {
        $sales = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start, $end]);
        $commission = DB::table('commissions')
            ->join('transaction_details', 'transaction_details.id', '=', 'commissions.transaction_detail_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween('transactions.transaction_date', [$start, $end]);

        return (object) [
            'total_sales' => (float) (clone $sales)->sum('total_amount'),
            'total_transactions' => (int) (clone $sales)->count(),
            'total_commission' => (float) $commission->sum('commissions.commission_amount'),
        ];
    }

    /** @return array{labels: array<int, string>, sales: array<int, float>, commissions: array<int, float>} */
    private function chart(ReportRange $range): array
    {
        $buckets = $range->chartBuckets();
        $expression = $this->chartBucketExpression($range->period);
        $start = $range->utcStart();
        $end = $range->utcEnd();

        $salesRows = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw("{$expression} as bucket, COALESCE(SUM(total_amount), 0) as amount")
            ->groupBy('bucket')
            ->get()
            ->keyBy(fn (object $row): string => (string) $row->bucket);

        $commissionRows = DB::table('commissions')
            ->join('transaction_details', 'transaction_details.id', '=', 'commissions.transaction_detail_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->selectRaw("{$expression} as bucket, COALESCE(SUM(commissions.commission_amount), 0) as amount")
            ->groupBy('bucket')
            ->get()
            ->keyBy(fn (object $row): string => (string) $row->bucket);

        return [
            'labels' => array_column($buckets, 'label'),
            'sales' => array_map(fn (array $bucket): float => round((float) ($salesRows->get((string) $bucket['key'])->amount ?? 0), 2), $buckets),
            'commissions' => array_map(fn (array $bucket): float => round((float) ($commissionRows->get((string) $bucket['key'])->amount ?? 0), 2), $buckets),
        ];
    }

    private function chartBucketExpression(string $period): string
    {
        $driver = DB::connection()->getDriverName();
        $localDateTime = match ($driver) {
            'mysql', 'mariadb' => "DATE_ADD(transactions.transaction_date, INTERVAL 8 HOUR)",
            'sqlite' => "datetime(transactions.transaction_date, '+8 hours')",
            'pgsql' => "(transactions.transaction_date + INTERVAL '8 hours')",
            'sqlsrv' => 'DATEADD(hour, 8, transactions.transaction_date)',
            default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
        };

        return match ($period) {
            'daily' => match ($driver) {
                'mysql', 'mariadb' => "HOUR({$localDateTime})",
                'sqlite' => "CAST(strftime('%H', {$localDateTime}) AS INTEGER)",
                'pgsql' => "CAST(EXTRACT(HOUR FROM {$localDateTime}) AS INTEGER)",
                'sqlsrv' => "DATEPART(hour, {$localDateTime})",
                default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
            },
            'yearly' => match ($driver) {
                'mysql', 'mariadb' => "MONTH({$localDateTime})",
                'sqlite' => "CAST(strftime('%m', {$localDateTime}) AS INTEGER)",
                'pgsql' => "CAST(EXTRACT(MONTH FROM {$localDateTime}) AS INTEGER)",
                'sqlsrv' => "DATEPART(month, {$localDateTime})",
                default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
            },
            default => match ($driver) {
                'mysql', 'mariadb' => "DATE({$localDateTime})",
                'sqlite' => "date({$localDateTime})",
                'pgsql' => "CAST({$localDateTime} AS DATE)",
                'sqlsrv' => "CAST({$localDateTime} AS date)",
                default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
            },
        };
    }

    private function topServices(ReportRange $range): Collection
    {
        return DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('services', 'services.id', '=', 'transaction_details.service_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween('transactions.transaction_date', [$range->utcStart(), $range->utcEnd()])
            ->select('services.service_name')
            ->selectRaw('COUNT(transaction_details.id) as times_availed, COALESCE(SUM(transaction_details.subtotal), 0) as total_sales')
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();
    }

    private function therapistCommissions(ReportRange $range): Collection
    {
        $details = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween('transactions.transaction_date', [$range->utcStart(), $range->utcEnd()])
            ->select('transaction_details.therapist_id')
            ->selectRaw('COUNT(transaction_details.id) as services_done, COALESCE(SUM(transaction_details.subtotal), 0) as sales')
            ->groupBy('transaction_details.therapist_id');
        $commissions = DB::table('commissions')
            ->join('transaction_details', 'transaction_details.id', '=', 'commissions.transaction_detail_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween('transactions.transaction_date', [$range->utcStart(), $range->utcEnd()])
            ->select('transaction_details.therapist_id')
            ->selectRaw('COALESCE(SUM(commissions.commission_amount), 0) as commission')
            ->groupBy('transaction_details.therapist_id');

        return Therapist::query()
            ->leftJoinSub($details, 'period_details', 'period_details.therapist_id', '=', 'therapists.id')
            ->leftJoinSub($commissions, 'period_commissions', 'period_commissions.therapist_id', '=', 'therapists.id')
            ->select('therapists.therapist_name')
            ->selectRaw('COALESCE(period_details.services_done, 0) as services_done, COALESCE(period_details.sales, 0) as sales, COALESCE(period_commissions.commission, 0) as commission')
            ->orderByDesc('sales')
            ->orderBy('therapists.therapist_name')
            ->get();
    }

    private function paymentMethods(ReportRange $range): array
    {
        $rows = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$range->utcStart(), $range->utcEnd()])
            ->select('payment_method')
            ->selectRaw('COUNT(*) as transactions, COALESCE(SUM(total_amount), 0) as total_sales')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        return collect(['cash', 'ecash'])->mapWithKeys(fn (string $method): array => [
            $method => [
                'count' => (int) ($rows->get($method)->transactions ?? 0),
                'total' => round((float) ($rows->get($method)->total_sales ?? 0), 2),
            ],
        ])->all();
    }

    private function chartSectionTitle(ReportRange $range): string
    {
        return match ($range->period) {
            'daily' => 'Sales and Commission by Hour',
            'yearly' => 'Sales and Commission by Month',
            default => 'Sales and Commission by Day',
        };
    }

    private function formatMoney(float $amount): string
    {
        return '₱'.number_format($amount, 2, '.', ',');
    }
}
