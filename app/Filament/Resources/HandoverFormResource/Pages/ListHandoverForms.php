<?php

namespace App\Filament\Resources\HandoverFormResource\Pages;

use App\Filament\Resources\HandoverFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHandoverForms extends ListRecords
{
    protected static string $resource = HandoverFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
