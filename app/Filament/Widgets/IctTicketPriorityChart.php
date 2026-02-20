<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\ChartWidget;

class IctTicketPriorityChart extends ChartWidget
{
    protected static ?string $heading = '⚡ Distribusi Prioritas';

    protected static ?string $description = 'Pembagian tiket berdasarkan tingkat prioritas';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    protected function getData(): array
    {
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $counts = [];

        foreach ($priorities as $priority) {
            $counts[] = IctTicket::where('priority', $priority)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#10b981', // Low - green
                        '#f59e0b', // Medium - amber
                        '#f97316', // High - orange
                        '#ef4444', // Critical - red
                    ],
                    'borderColor' => [
                        '#059669',
                        '#d97706',
                        '#ea580c',
                        '#dc2626',
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => ['🟢 Low', '🟡 Medium', '🟠 High', '🔴 Critical'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
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