<?php

namespace App\Filament\Resources\DivisionCoordinatorResource\Pages;

use App\Filament\Resources\DivisionCoordinatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivisionCoordinator extends EditRecord
{
    protected static string $resource = DivisionCoordinatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
