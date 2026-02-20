<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IctRecentTicketsWidget extends BaseWidget
{
    protected static ?string $heading = '🎫 Tiket Terbaru';

    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
            IctTicket::query()
            ->with(['reporter', 'assignee'])
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
}