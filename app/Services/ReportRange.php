<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class ReportRange
{
    public readonly string $period;

    public readonly CarbonImmutable $selectedDate;

    public readonly CarbonImmutable $start;

    public readonly CarbonImmutable $end;

    public readonly CarbonImmutable $previousStart;

    public readonly CarbonImmutable $previousEnd;

    public readonly string $label;

    private function __construct(string $period, CarbonImmutable $selectedDate)
    {
        $this->period = $period;
        $this->selectedDate = $selectedDate;
        $this->start = match ($period) {
            'weekly' => $selectedDate->startOfWeek(CarbonImmutable::MONDAY),
            'monthly' => $selectedDate->startOfMonth(),
            'yearly' => $selectedDate->startOfYear(),
            default => $selectedDate->startOfDay(),
        };
        $this->end = match ($period) {
            'weekly' => $this->start->addDays(6)->endOfDay(),
            'monthly' => $this->start->endOfMonth()->endOfDay(),
            'yearly' => $this->start->endOfYear()->endOfDay(),
            default => $this->start->endOfDay(),
        };
        $this->previousStart = match ($period) {
            'weekly' => $this->start->subWeek(),
            'monthly' => $this->start->subMonthNoOverflow()->startOfMonth(),
            'yearly' => $this->start->subYear()->startOfYear(),
            default => $this->start->subDay(),
        };
        $this->previousEnd = match ($period) {
            'weekly' => $this->previousStart->addDays(6)->endOfDay(),
            'monthly' => $this->previousStart->endOfMonth()->endOfDay(),
            'yearly' => $this->previousStart->endOfYear()->endOfDay(),
            default => $this->previousStart->endOfDay(),
        };
        $this->label = $this->makeLabel();
    }

    public static function make(?string $period, ?string $date): self
    {
        $period = in_array($period, ['daily', 'weekly', 'monthly', 'yearly'], true) ? $period : 'daily';
        $timezone = 'Asia/Manila';
        $selected = null;

        try {
            if ($date !== null && $date !== '') {
                $format = match ($period) {
                    'monthly' => preg_match('/^\d{4}-\d{2}$/', $date) ? '!Y-m' : '!Y-m-d',
                    'yearly' => preg_match('/^\d{4}$/', $date) ? '!Y' : '!Y-m-d',
                    default => '!Y-m-d',
                };
                $selected = CarbonImmutable::createFromFormat($format, $date, $timezone);
                if ($selected && $selected->format(ltrim($format, '!')) !== $date) {
                    $selected = null;
                }
            }
        } catch (\Throwable) {
            $selected = null;
        }

        $selected ??= CarbonImmutable::now($timezone)->startOfDay();

        return new self($period, $selected);
    }

    public function inputDate(): string
    {
        return match ($this->period) {
            'monthly' => $this->selectedDate->format('Y-m'),
            'yearly' => $this->selectedDate->format('Y'),
            default => $this->selectedDate->format('Y-m-d'),
        };
    }

    public function utcStart(): CarbonImmutable
    {
        return $this->start->setTimezone('UTC');
    }

    public function utcEnd(): CarbonImmutable
    {
        return $this->end->setTimezone('UTC');
    }

    public function utcPreviousStart(): CarbonImmutable
    {
        return $this->previousStart->setTimezone('UTC');
    }

    public function utcPreviousEnd(): CarbonImmutable
    {
        return $this->previousEnd->setTimezone('UTC');
    }

    /** @return array<int, array{key: string|int, label: string}> */
    public function chartBuckets(): array
    {
        return match ($this->period) {
            'daily' => array_map(fn (int $hour): array => ['key' => $hour, 'label' => CarbonImmutable::createFromTime($hour, 0, 0, 'Asia/Manila')->format('g A')], range(11, 23)),
            'yearly' => array_map(fn (int $month): array => ['key' => $month, 'label' => CarbonImmutable::createFromDate((int) $this->start->format('Y'), $month, 1, 'Asia/Manila')->format('M')], range(1, 12)),
            default => $this->dayBuckets(),
        };
    }

    private function dayBuckets(): array
    {
        $days = [];
        for ($date = $this->start; $date->lte($this->end); $date = $date->addDay()) {
            $days[] = ['key' => $date->format('Y-m-d'), 'label' => $this->period === 'weekly' ? $date->format('D') : $date->format('j')];
        }

        return $days;
    }

    private function makeLabel(): string
    {
        return match ($this->period) {
            'weekly' => $this->start->format('D M j').' - '.$this->end->format('D M j, Y'),
            'monthly' => $this->start->format('M j').' - '.$this->end->format('M j, Y'),
            'yearly' => $this->start->format('M j').' - '.$this->end->format('M j, Y'),
            default => $this->start->format('D M j, Y'),
        };
    }
}
