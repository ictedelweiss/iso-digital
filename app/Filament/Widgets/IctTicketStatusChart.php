<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\ChartWidget;

class IctTicketStatusChart extends ChartWidget
{
    protected static ?string $heading = '📋 Distribusi Status';

    protected static ?string $description = 'Status semua tiket saat ini';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    protected function getData(): array
    {
        $statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = IctTicket::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6', // Open - blue
                        '#f59e0b', // In Progress - amber
                        '#10b981', // Resolved - green
                        '#6b7280', // Closed - gray
                    ],
                    'borderColor' => '#1f2937',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['🔵 Open', '🟡 In Progress', '✅ Resolved', '⬜ Closed'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
        ];
    }
}