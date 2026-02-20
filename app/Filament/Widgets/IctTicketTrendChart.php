<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class IctTicketTrendChart extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Tiket Bulanan';

    protected static ?string $description = 'Jumlah tiket dibuat vs diselesaikan per bulan';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    protected function getData(): array
    {
        $labels = [];
        $created = [];
        $resolved = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $labels[] = $date->translatedFormat('M Y');

            $created[] = IctTicket::whereBetween('created_at', [$start, $end])->count();

            $resolved[] = IctTicket::whereNotNull('resolved_at')
                ->whereBetween('resolved_at', [$start, $end])
                ->count();
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
}