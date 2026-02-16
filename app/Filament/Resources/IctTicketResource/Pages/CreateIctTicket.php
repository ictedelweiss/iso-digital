<?php

namespace App\Filament\Resources\IctTicketResource\Pages;

use App\Filament\Resources\IctTicketResource;
use App\Mail\NewTicketNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateIctTicket extends CreateRecord
{
    protected static string $resource = IctTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        // Kirim email notifikasi ke ICT
        try {
            Mail::to('ict@edelweiss.sch.id')->send(
                new NewTicketNotification($this->record)
            );
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send ticket notification: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}