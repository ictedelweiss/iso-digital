<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class IctHelpdeskDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $navigationGroup = 'ICT Helpdesk';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'ICT Helpdesk Dashboard';

    protected static ?string $slug = 'ict-helpdesk-dashboard';

    protected static string $view = 'filament.pages.ict-helpdesk-dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\IctHelpdeskStatsWidget::class ,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\IctTicketTrendChart::class ,
            \App\Filament\Widgets\IctTicketCategoryChart::class ,
            \App\Filament\Widgets\IctTicketPriorityChart::class ,
            \App\Filament\Widgets\IctTicketStatusChart::class ,
            \App\Filament\Widgets\IctRecentTicketsWidget::class ,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}