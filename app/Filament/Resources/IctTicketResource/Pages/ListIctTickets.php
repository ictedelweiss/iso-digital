<?php

namespace App\Filament\Resources\IctTicketResource\Pages;

use App\Filament\Resources\IctTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIctTickets extends ListRecords
{
    protected static string $resource = IctTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Tiket Baru'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\IctHelpdeskStatsWidget::class ,
        ];
    }
}