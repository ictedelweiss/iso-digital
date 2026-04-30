<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class IctTicketTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '📈 Tren Tiket';

    protected static ?string $description = 'Jumlah tiket dibuat vs diselesaikan';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();
        $diffDays = $startDate->diffInDays($endDate);

        $labels = [];
        $created = [];
        $resolved = [];

        if ($diffDays <= 14) {
            // Daily view
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $labels[] = $current->format('d M');
                $created[] = IctTicket::whereDate('created_at', $current)->count();
                $resolved[] = IctTicket::whereNotNull('resolved_at')
                    ->whereDate('resolved_at', $current)->count();
                $current->addDay();
            }
        }
        elseif ($diffDays <= 90) {
            // Weekly view
            $current = $startDate->copy()->startOfWeek();
            while ($current->lte($endDate)) {
                $weekEnd = $current->copy()->endOfWeek()->min($endDate);
                $labels[] = $current->format('d M');
                $created[] = IctTicket::whereBetween('created_at', [$current, $weekEnd])->count();
                $resolved[] = IctTicket::whereNotNull('resolved_at')
                    ->whereBetween('resolved_at', [$current, $weekEnd])->count();
                $current->addWeek();
            }
        }
        else {
            // Monthly view
            $current = $startDate->copy()->startOfMonth();
            while ($current->lte($endDate)) {
                $monthEnd = $current->copy()->endOfMonth()->min($endDate);
                $labels[] = $current->translatedFormat('M Y');
                $created[] = IctTicket::whereBetween('created_at', [$current, $monthEnd])->count();
                $resolved[] = IctTicket::whereNotNull('resolved_at')
                    ->whereBetween('resolved_at', [$current, $monthEnd])->count();
                $current->addMonth();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Dibuat',
                    'data' => $created,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Tiket Resolved',
                    'data' => $resolved,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                ],
            ],
        ];
    }

    private function getDateRange(): array
    {
        $period = $this->filters['period'] ?? 'this_month';
        $now = Carbon::now();

        return match ($period) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
                'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
                'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
                'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
                'last_3_months' => [$now->copy()->subMonths(3)->startOfMonth(), $now->copy()->endOfMonth()],
                'last_6_months' => [$now->copy()->subMonths(6)->startOfMonth(), $now->copy()->endOfMonth()],
                'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
                'custom' => [
                Carbon::parse($this->filters['start_date'] ?? $now->copy()->startOfMonth()),
                Carbon::parse($this->filters['end_date'] ?? $now->copy()->endOfMonth())->endOfDay(),
            ],
                default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            };
    }
}