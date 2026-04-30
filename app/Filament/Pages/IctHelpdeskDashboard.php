<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IctHelpdeskStatsWidget;
use App\Filament\Widgets\IctTicketTrendChart;
use App\Filament\Widgets\IctTicketCategoryChart;
use App\Filament\Widgets\IctTicketPriorityChart;
use App\Filament\Widgets\IctTicketStatusChart;
use App\Filament\Widgets\IctRecentTicketsWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;

class IctHelpdeskDashboard extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $navigationGroup = 'ICT Helpdesk';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'ICT Helpdesk Dashboard';

    protected static ?string $slug = 'ict-helpdesk-dashboard';

    protected static string $view = 'filament.pages.ict-helpdesk-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAccessTo('ict_helpdesk') ?? false;
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
            Section::make()
            ->schema([
                Select::make('period')
                ->label('Periode')
                ->options([
                    'today' => '📅 Hari Ini',
                    'this_week' => '📆 Minggu Ini',
                    'this_month' => '🗓️ Bulan Ini',
                    'last_month' => '📋 Bulan Lalu',
                    'last_3_months' => '📊 3 Bulan Terakhir',
                    'last_6_months' => '📈 6 Bulan Terakhir',
                    'this_year' => '🗓️ Tahun Ini',
                    'custom' => '🔧 Custom Range',
                ])
                ->default('this_month')
                ->native(false)
                ->live(),

                DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->native(false)
                ->displayFormat('d M Y')
                ->visible(fn($get) => $get('period') === 'custom'),

                DatePicker::make('end_date')
                ->label('Tanggal Selesai')
                ->native(false)
                ->displayFormat('d M Y')
                ->visible(fn($get) => $get('period') === 'custom'),
            ])
            ->columns(3)
            ->compact(),
        ]);
    }

    public function getVisibleWidgets(): array
    {
        return [
            IctHelpdeskStatsWidget::class ,
            IctTicketTrendChart::class ,
            IctTicketCategoryChart::class ,
            IctTicketPriorityChart::class ,
            IctTicketStatusChart::class ,
            IctRecentTicketsWidget::class ,
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }

    public function getWidgetData(): array
    {
        return [];
    }
}
