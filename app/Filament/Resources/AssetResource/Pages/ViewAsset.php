<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('view_history')
                ->label('Lihat History')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->modalHeading('History Perubahan Asset')
                ->modalContent(view('filament.resources.asset-resource.view-history', [
                    'asset' => $this->record,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->slideOver(),
        ];
    }
}
