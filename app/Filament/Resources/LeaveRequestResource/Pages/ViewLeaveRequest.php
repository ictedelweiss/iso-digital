<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ApprovalStatusWidget::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                // Check if user can approve, record isn't fully approved, and isn't rejected
                ->visible(fn($record) => (new \App\Services\ApprovalService)->canApprove($record) && !$record->isFullyApproved() && $record->status !== 'Rejected')
                ->action(function ($record) {
                    (new \App\Services\ApprovalService)->approve($record);
                    \Filament\Notifications\Notification::make()
                        ->title('Leave Request Approved Successfully')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Rejection')
                        ->required()
                ])
                ->visible(fn($record) => (new \App\Services\ApprovalService)->canApprove($record) && $record->status !== 'Rejected' && !$record->isFullyApproved())
                ->action(function ($record, array $data) {
                    (new \App\Services\ApprovalService)->reject($record, $data['reason']);
                    \Filament\Notifications\Notification::make()
                        ->title('Leave Request Rejected')
                        ->danger()
                        ->send();
                }),
            Actions\Action::make('resend_email')
                ->label('Resend Approval Email')
                ->color('info')
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->visible(fn($record) => !$record->isFullyApproved() && $record->status !== 'Rejected')
                ->action(function ($record) {
                    (new \App\Services\ApprovalService)->sendNotification($record, 'next_step');
                    \Filament\Notifications\Notification::make()
                        ->title('Approval Email Resent Successfully')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('print')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->url(fn($record) => route('leave.pdf', $record))
                ->openUrlInNewTab(),
        ];
    }
}
