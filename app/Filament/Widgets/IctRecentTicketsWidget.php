<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class IctRecentTicketsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '🎫 Tiket Terbaru';

    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        [$startDate, $endDate] = $this->getDateRange();

        return $table
            ->query(
            IctTicket::query()
            ->with(['reporter', 'assignee'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(10)
        )
            ->columns([
            Tables\Columns\TextColumn::make('ticket_number')
            ->label('No. Tiket')
            ->weight('bold')
            ->searchable(),

            Tables\Columns\TextColumn::make('subject')
            ->label('Subject')
            ->limit(35)
            ->tooltip(fn(IctTicket $record): string => $record->subject),

            Tables\Columns\TextColumn::make('reporter.display_name')
            ->label('Pelapor')
            ->limit(20),

            Tables\Columns\TextColumn::make('category')
            ->label('Kategori')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
            'Hardware' => 'info',
            'Software' => 'success',
            'Network' => 'warning',
            'Account' => 'gray',
            default => 'gray',
        }),

            Tables\Columns\TextColumn::make('priority')
            ->label('Prioritas')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
            'Low' => 'success',
            'Medium' => 'warning',
            'High' => 'danger',
            'Critical' => 'danger',
            default => 'gray',
        })
            ->icon(fn(string $state): string => match ($state) {
            'Critical' => 'heroicon-o-fire',
            'High' => 'heroicon-o-exclamation-triangle',
            'Medium' => 'heroicon-o-clock',
            'Low' => 'heroicon-o-minus-circle',
            default => 'heroicon-o-question-mark-circle',
        }),

            Tables\Columns\TextColumn::make('status')
            ->label('Status')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
            'Open' => 'info',
            'In Progress' => 'warning',
            'Resolved' => 'success',
            'Closed' => 'gray',
            default => 'gray',
        }),

            Tables\Columns\TextColumn::make('created_at')
            ->label('Dibuat')
            ->dateTime('d M Y, H:i')
            ->sortable(),

            Tables\Columns\IconColumn::make('sla_status')
            ->label('SLA')
            ->state(fn(IctTicket $record): string => $record->getIsSlaBreach() ? 'breach' : 'ok')
            ->icon(fn(string $state): string => match ($state) {
            'breach' => 'heroicon-o-exclamation-triangle',
            'ok' => 'heroicon-o-check-circle',
        })
            ->color(fn(string $state): string => match ($state) {
            'breach' => 'danger',
            'ok' => 'success',
        }),
        ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
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