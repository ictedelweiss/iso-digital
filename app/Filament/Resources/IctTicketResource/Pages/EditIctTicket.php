<?php

namespace App\Filament\Resources\IctTicketResource\Pages;

use App\Filament\Resources\IctTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIctTicket extends EditRecord
{
    protected static string $resource = IctTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}