<?php

namespace App\Filament\Resources\IctTicketResource\Pages;

use App\Filament\Resources\IctTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIctTicket extends ViewRecord
{
    protected static string $resource = IctTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}