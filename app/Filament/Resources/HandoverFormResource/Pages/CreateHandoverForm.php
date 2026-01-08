<?php

namespace App\Filament\Resources\HandoverFormResource\Pages;

use App\Filament\Resources\HandoverFormResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHandoverForm extends CreateRecord
{
    protected static string $resource = HandoverFormResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\HandoverForm $record */
        $record = $this->getRecord();

        // Trigger generic approval notification
        (new \App\Services\ApprovalService)->sendNotification($record, 'created');

        // Optional: Send success notification to UI
        \Filament\Notifications\Notification::make()
            ->title('Handover Form Submitted Successfully')
            ->success()
            ->send();
    }
}
