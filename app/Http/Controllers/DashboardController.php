<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Therapist;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $now = CarbonImmutable::now('Asia/Manila');
        $today = $now->toDateString();
        $todayStartUtc = $now->startOfDay()->utc();
        $todayEndUtc = $now->endOfDay()->utc();
        $salesToday = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$todayStartUtc, $todayEndUtc])
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total_amount), 0) as total_sales')
            ->first();

        $appointmentsToday = DB::table('appointments')->whereDate('appointment_date', $today)->where('appointment_status', '!=', 'cancelled');
        $appointmentCount = (clone $appointmentsToday)->count();
        $completedAppointments = (clone $appointmentsToday)->where('appointment_status', 'completed')->count();
        $pendingAppointments = DB::table('appointments')
            ->whereDate('appointment_date', '>=', $today)
            ->whereIn('appointment_status', ['pending', 'confirmed'])
            ->count();

        $therapistCount = Therapist::query()->count();
        $availableTherapistCount = Therapist::query()->where('availability_status', 'available')->count();
        $adminExtras = null;
        if ($request->user()->isAdmin()) {
            $adminExtras = [
                'commission_today' => (float) DB::table('commissions')
                    ->join('transaction_details', 'transaction_details.id', '=', 'commissions.transaction_detail_id')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->where('transactions.payment_status', 'paid')
                    ->whereDate('commissions.commission_date', $today)
                    ->sum('commissions.commission_amount'),
                'sales_this_month' => (float) DB::table('transactions')
                    ->where('payment_status', 'paid')
                    ->whereBetween('transaction_date', [$now->startOfMonth()->utc(), $now->endOfMonth()->utc()])
                    ->sum('total_amount'),
            ];
        }

        $upcomingAppointments = $this->upcomingAppointments($today, $now);
        $therapists = Therapist::query()
            ->with(['appointments' => static function ($query) use ($today): void {
                $query->where('appointment_date', $today)
                    ->where('appointment_status', 'in_service')
                    ->with('service')
                    ->orderBy('appointment_time');
            }])
            ->orderBy('therapist_name')
            ->get();
        $weekly = $this->weeklySales($now);

        $viewData = [
            'todayLabel' => $now->format('l, M j, Y'),
            'todaySales' => round((float) $salesToday->total_sales, 2),
            'paidTransactionsToday' => (int) $salesToday->transaction_count,
            'todayAppointments' => $appointmentCount,
            'completedAppointments' => $completedAppointments,
            'pendingAppointments' => $pendingAppointments,
            'availableTherapists' => $availableTherapistCount,
            'therapistCount' => $therapistCount,
            'upcomingAppointments' => $upcomingAppointments,
            'therapists' => $therapists,
            'weeklyLabels' => $weekly['labels'],
            'weeklyValues' => $weekly['values'],
            'weeklyTotal' => $weekly['total'],
            'hasWeeklySales' => $weekly['total'] > 0,
        ];
        if ($adminExtras !== null) {
            $viewData['adminExtras'] = $adminExtras;
        }

        return view('dashboard', $viewData);
    }

    private function upcomingAppointments(string $today, CarbonImmutable $now)
    {
        $endExpression = $this->appointmentEndExpression();

        return Appointment::query()
            ->join('services', 'services.id', '=', 'appointments.service_id')
            ->where('appointments.appointment_date', $today)
            ->whereIn('appointments.appointment_status', ['pending', 'confirmed', 'arrived', 'in_service'])
            ->whereRaw("{$endExpression} >= ?", [$now->format('Y-m-d H:i:s')])
            ->with(['customer', 'service', 'therapist'])
            ->select('appointments.*')
            ->orderBy('appointments.appointment_time')
            ->limit(8)
            ->get();
    }

    private function appointmentEndExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => 'TIMESTAMP(appointments.appointment_date, appointments.appointment_time) + INTERVAL services.duration MINUTE',
            'sqlite' => "datetime(appointments.appointment_date || ' ' || appointments.appointment_time, '+' || services.duration || ' minutes')",
            'pgsql' => "appointments.appointment_date + appointments.appointment_time + (services.duration * INTERVAL '1 minute')",
            'sqlsrv' => "DATEADD(minute, DATEDIFF(minute, CAST('00:00:00' AS time), appointments.appointment_time) + services.duration, CAST(appointments.appointment_date AS datetime2))",
            default => throw new \RuntimeException('Unsupported database driver for appointment timing.'),
        };
    }

    /** @return array{labels: array<int, string>, values: array<int, float>, total: float} */
    private function weeklySales(CarbonImmutable $now): array
    {
        $start = $now->subDays(6)->startOfDay();
        $end = $now->endOfDay();
        $dateExpression = match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => 'DATE(DATE_ADD(transaction_date, INTERVAL 8 HOUR))',
            'sqlite' => "date(datetime(transaction_date, '+8 hours'))",
            'pgsql' => "CAST(transaction_date + INTERVAL '8 hours' AS DATE)",
            'sqlsrv' => 'CAST(DATEADD(hour, 8, transaction_date) AS date)',
            default => throw new \RuntimeException('Unsupported database driver for weekly sales.'),
        };
        $grouped = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start->utc(), $end->utc()])
            ->selectRaw("{$dateExpression} as sales_day, COALESCE(SUM(total_amount), 0) as total_sales")
            ->groupBy('sales_day')
            ->get()
            ->keyBy(fn (object $row): string => CarbonImmutable::parse($row->sales_day, 'Asia/Manila')->toDateString());

        $labels = [];
        $values = [];
        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $labels[] = $day->format('D j');
            $values[] = round((float) ($grouped->get($day->toDateString())->total_sales ?? 0), 2);
        }
        $total = (float) DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start->utc(), $end->utc()])
            ->sum('total_amount');

        return ['labels' => $labels, 'values' => $values, 'total' => round($total, 2)];
    }
}
