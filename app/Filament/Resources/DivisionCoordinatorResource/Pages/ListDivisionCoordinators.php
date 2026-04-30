<?php

namespace App\Filament\Resources\DivisionCoordinatorResource\Pages;

use App\Filament\Resources\DivisionCoordinatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisionCoordinators extends ListRecords
{
    protected static string $resource = DivisionCoordinatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
