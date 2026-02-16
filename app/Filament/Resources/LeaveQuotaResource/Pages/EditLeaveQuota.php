<?php

namespace App\Filament\Resources\LeaveQuotaResource\Pages;

use App\Filament\Resources\LeaveQuotaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveQuota extends EditRecord
{
    protected static string $resource = LeaveQuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
