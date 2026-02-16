<?php

namespace App\Filament\Resources\PurchaseRequisitionResource\Pages;

use App\Filament\Resources\PurchaseRequisitionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\PurchaseRequisition $record */
        $record = $this->getRecord();

        // Trigger generic approval notification
        (new \App\Services\ApprovalService)->sendNotification($record, 'created');

        // Optional: Send success notification to UI
        \Filament\Notifications\Notification::make()
            ->title('PR Created Successfully')
            ->success()
            ->send();
    }
}
