<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionCoordinatorResource\Pages;
use App\Models\DivisionCoordinator;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DivisionCoordinatorResource extends Resource
{
    protected static ?string $model = DivisionCoordinator::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Koordinator Divisi';

    protected static ?string $modelLabel = 'Koordinator Divisi';

    protected static ?string $pluralModelLabel = 'Koordinator Divisi';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Koordinator')
                    ->schema([
                        Forms\Components\Select::make('department')
                            ->label('Divisi / Departemen')
                            ->options(array_combine(config('approval.departments', []), config('approval.departments', [])))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->searchable(),

                        Forms\Components\Select::make('user_id')
                            ->label('User Koordinator')
                            ->options(function () {
                                return User::query()
                                    ->orderByRaw('COALESCE(display_name, username)')
                                    ->get()
                                    ->mapWithKeys(fn (User $user) => [$user->id => $user->display_name ?: ucwords(str_replace('.', ' ', $user->username))]);
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?int $state) {
                                if (! $state) {
                                    return;
                                }

                                $user = User::find($state);

                                if (! $user) {
                                    return;
                                }

                                $set('coordinator_name', $user->display_name ?: ucwords(str_replace('.', ' ', $user->username)));
                                $set('coordinator_email', $user->email);
                            }),

                        Forms\Components\TextInput::make('coordinator_name')
                            ->label('Nama Koordinator')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('coordinator_email')
                            ->label('Email Koordinator')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('department')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('coordinator_name')
                    ->label('Koordinator')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coordinator_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.display_name')
                    ->label('Linked User')
                    ->state(fn (DivisionCoordinator $record) => $record->user?->display_name ?: $record->user?->username ?: '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('department')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisionCoordinators::route('/'),
            'create' => Pages\CreateDivisionCoordinator::route('/create'),
            'edit' => Pages\EditDivisionCoordinator::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAccessTo('user_management') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }
}
