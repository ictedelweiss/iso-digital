<?php

namespace App\Filament\Resources\HandoverFormResource\Pages;

use App\Filament\Resources\HandoverFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHandoverForm extends EditRecord
{
    protected static string $resource = HandoverFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
