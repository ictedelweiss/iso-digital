<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class IctTicketCategoryChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '📂 Distribusi Kategori';

    protected static ?string $description = 'Pembagian tiket berdasarkan kategori';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $categories = ['Hardware', 'Software', 'Network', 'Account'];
        $counts = [];

        foreach ($categories as $category) {
            $counts[] = IctTicket::where('category', $category)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6',
                    ],
                    'borderColor' => '#1f2937',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['🖥️ Hardware', '💿 Software', '🌐 Network', '🔑 Account'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
            'cutout' => '60%',
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