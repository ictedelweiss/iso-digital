<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HandoverFormResource\Pages;
use App\Models\HandoverForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class HandoverFormResource extends Resource
{
    protected static ?string $model = HandoverForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Handover Forms';

    protected static ?string $modelLabel = 'Handover';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAccessTo('documents') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Information')
                    ->schema([
                        Forms\Components\TextInput::make('item_name')
                            ->label('Item Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\DatePicker::make('handover_date')
                            ->label('Handover Date')
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\Textarea::make('specification')
                            ->label('Specifications')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('item_condition')
                            ->label('Condition')
                            ->options([
                                'Baru' => 'Baru (New)',
                                'Baik' => 'Baik (Good)',
                                'Cukup Baik' => 'Cukup Baik (Fair)',
                                'Perlu Perbaikan' => 'Perlu Perbaikan (Needs Repair)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('loan_period')
                            ->label('Loan Period')
                            ->placeholder('e.g., 1 year, Permanent')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Recipient Information')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Recipient Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('recipient_department')
                            ->label('Department')
                            ->options(array_combine(config('approval.departments', []), config('approval.departments', [])))
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Pending')
                            ->required(),
                        Forms\Components\TextInput::make('current_approval_step')
                            ->label('Current Step')
                            ->numeric()
                            ->default(1)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('S/N')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Recipient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_department')
                    ->label('Department')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('handover_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('recipient_department')
                    ->label('Department')
                    ->options(array_combine(config('approval.departments', []), config('approval.departments', []))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(\App\Models\HandoverForm $record) => route('handover.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHandoverForms::route('/'),
            'create' => Pages\CreateHandoverForm::route('/create'),
            'view' => Pages\ViewHandoverForm::route('/{record}'),
            'edit' => Pages\EditHandoverForm::route('/{record}/edit'),
        ];
    }
}
