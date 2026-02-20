<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\ChartWidget;

class IctTicketCategoryChart extends ChartWidget
{
    protected static ?string $heading = '📂 Distribusi Kategori';

    protected static ?string $description = 'Pembagian tiket berdasarkan kategori';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    protected function getData(): array
    {
        $categories = ['Hardware', 'Software', 'Network', 'Account'];
        $counts = [];

        foreach ($categories as $category) {
            $counts[] = IctTicket::where('category', $category)->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6', // Hardware - blue
                        '#10b981', // Software - green
                        '#f59e0b', // Network - amber
                        '#8b5cf6', // Account - violet
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
}