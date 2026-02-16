<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\LeaveRequest $record */
        $record = $this->getRecord();

        // Trigger generic approval notification
        (new \App\Services\ApprovalService)->sendNotification($record, 'created');

        // Optional: Send success notification to UI
        \Filament\Notifications\Notification::make()
            ->title('Leave Request Submitted Successfully')
            ->success()
            ->send();
    }
}
