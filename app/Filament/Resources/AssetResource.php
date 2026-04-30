<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Asset';

    protected static ?string $modelLabel = 'Asset';

    protected static ?string $pluralModelLabel = 'Asset';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAccessTo('asset_management') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('asset_code')
                            ->label('Kode Asset')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated')
                            ->helperText('Kode akan otomatis dibuat setelah menyimpan')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Asset')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('Contoh: Laptop Dell Latitude 5420')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->required()
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('prefix')
                                    ->required()
                                    ->maxLength(10),
                                Forms\Components\Textarea::make('description'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Select::make('location_id')
                            ->label('Lokasi')
                            ->required()
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(10),
                                Forms\Components\Textarea::make('address'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Select::make('pic_id')
                            ->label('PIC (Person In Charge)')
                            ->relationship('pic', 'username')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih PIC')
                            ->helperText('Penanggung jawab asset')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Teknis')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->placeholder('S/N perangkat'),

                        Forms\Components\TextInput::make('model')
                            ->label('Model/Tipe')
                            ->maxLength(100)
                            ->placeholder('Contoh: Latitude 5420'),

                        Forms\Components\TextInput::make('manufacturer')
                            ->label('Manufaktur/Produsen')
                            ->maxLength(100)
                            ->placeholder('Contoh: Dell, HP, Lenovo'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Keuangan')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Harga Pembelian')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->placeholder('0.00'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'Active' => 'Active',
                                'Maintenance' => 'Maintenance',
                                'Retired' => 'Retired',
                            ])
                            ->default('Active')
                            ->native(false),

                        Forms\Components\Select::make('condition')
                            ->label('Kondisi')
                            ->required()
                            ->options([
                                'Excellent' => 'Excellent',
                                'Good' => 'Good',
                                'Fair' => 'Fair',
                                'Poor' => 'Poor',
                            ])
                            ->default('Good')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->helperText('Catatan internal tambahan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->label('Kode Asset')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->icon('heroicon-m-qr-code'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pic.username')
                    ->label('PIC')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->placeholder('Belum ditentukan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Maintenance' => 'warning',
                        'Retired' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Excellent' => 'success',
                        'Good' => 'info',
                        'Fair' => 'warning',
                        'Poor' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Active' => 'Active',
                        'Maintenance' => 'Maintenance',
                        'Retired' => 'Retired',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'Excellent' => 'Excellent',
                        'Good' => 'Good',
                        'Fair' => 'Fair',
                        'Poor' => 'Poor',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download_qr')
                    ->label('Download QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (Asset $record) {
                        $qrCodeData = base64_decode($record->qr_code);
                        $fileName = 'QR-' . $record->asset_code . '.svg';

                        return response()->streamDownload(function () use ($qrCodeData) {
                            echo $qrCodeData;
                        }, $fileName, [
                            'Content-Type' => 'image/svg+xml',
                        ]);
                    })
                    ->visible(fn(Asset $record) => !empty($record->qr_code)),

                Tables\Actions\Action::make('print_label')
                    ->label('Print Label')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Asset $record) => route('assets.print.single', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()->is_admin ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('print_labels')
                        ->label('Print Selected Labels')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function ($records) {
                            $assetIds = $records->pluck('id')->toArray();
                            return redirect()->route('assets.print.labels', ['assets' => $assetIds]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->is_admin ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('QR Code & Kode Asset')
                    ->schema([
                        Infolists\Components\ImageEntry::make('qr_code')
                            ->label('')
                            ->getStateUsing(fn(Asset $record) => 'data:image/svg+xml;base64,' . $record->qr_code)
                            ->size(300)
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('asset_code')
                            ->label('Kode Asset')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Informasi Asset')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama'),

                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('location.name')
                            ->label('Lokasi')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('pic.username')
                            ->label('PIC')
                            ->badge()
                            ->color('gray')
                            ->icon('heroicon-o-user')
                            ->placeholder('Belum ditentukan'),

                        Infolists\Components\TextEntry::make('serial_number')
                            ->label('Serial Number')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('model')
                            ->label('Model')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('manufacturer')
                            ->label('Manufaktur')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Active' => 'success',
                                'Maintenance' => 'warning',
                                'Retired' => 'danger',
                            }),

                        Infolists\Components\TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Excellent' => 'success',
                                'Good' => 'info',
                                'Fair' => 'warning',
                                'Poor' => 'danger',
                            }),

                        Infolists\Components\TextEntry::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->date('d F Y')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('purchase_price')
                            ->label('Harga Pembelian')
                            ->money('IDR')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Informasi Audit')
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d F Y, H:i'),

                        Infolists\Components\TextEntry::make('updater.name')
                            ->label('Diupdate Oleh')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Tanggal Update')
                            ->dateTime('d F Y, H:i'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
