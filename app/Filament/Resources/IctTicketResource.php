<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IctTicketResource\Pages;
use App\Models\IctTicket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class IctTicketResource extends Resource
{
    protected static ?string $model = IctTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $navigationGroup = 'ICT Helpdesk';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['Open', 'In Progress'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::whereIn('status', ['Open', 'In Progress'])->count();
        return $count > 5 ? 'danger' : ($count > 0 ? 'warning' : 'success');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tiket')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('No. Tiket')
                            ->disabled()
                            ->dehydrated(false)
                            ->hidden(fn(string $context): bool => $context === 'create'),

                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'Hardware' => '🖥️ Hardware',
                                'Software' => '💿 Software',
                                'Network' => '🌐 Network',
                                'Account' => '🔑 Account',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'Low' => '🟢 Low',
                                'Medium' => '🟡 Medium',
                                'High' => '🟠 High',
                                'Critical' => '🔴 Critical',
                            ])
                            ->default('Medium')
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Masalah')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('Lampiran')
                            ->directory('helpdesk-attachments')
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Penugasan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Open' => 'Open',
                                'In Progress' => 'In Progress',
                                'Resolved' => 'Resolved',
                                'Closed' => 'Closed',
                            ])
                            ->default('Open')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('assigned_to')
                            ->label('Ditugaskan ke')
                            ->relationship('assignee', 'display_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Placeholder::make('resolved_at_display')
                            ->label('Waktu Resolved')
                            ->content(fn(?IctTicket $record): string => $record?->resolved_at?->format('d M Y, H:i') ?? '-')
                            ->hidden(fn(string $context): bool => $context === 'create'),
                    ])
                    ->columns(3)
                    ->hidden(fn(string $context): bool => $context === 'create'),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn(IctTicket $record): string => $record->subject),

                Tables\Columns\TextColumn::make('reporter.display_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hardware' => 'info',
                        'Software' => 'success',
                        'Network' => 'warning',
                        'Account' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Low' => 'success',
                        'Medium' => 'warning',
                        'High' => 'danger',
                        'Critical' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Critical' => 'heroicon-o-fire',
                        'High' => 'heroicon-o-exclamation-triangle',
                        'Medium' => 'heroicon-o-clock',
                        'Low' => 'heroicon-o-minus-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Open' => 'info',
                        'In Progress' => 'warning',
                        'Resolved' => 'success',
                        'Closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignee.display_name')
                    ->label('Ditugaskan')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('sla_status')
                    ->label('SLA')
                    ->state(fn(IctTicket $record): string => $record->getIsSlaBreach() ? 'breach' : 'ok')
                    ->icon(fn(string $state): string => match ($state) {
                        'breach' => 'heroicon-o-exclamation-triangle',
                        'ok' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'breach' => 'danger',
                        'ok' => 'success',
                    })
                    ->tooltip(fn(IctTicket $record): string => $record->getIsSlaBreach()
                        ? '⚠️ SLA Breach! Lebih dari ' . $record->sla_limit_hours . ' jam'
                        : '✅ Dalam SLA'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Open' => 'Open',
                        'In Progress' => 'In Progress',
                        'Resolved' => 'Resolved',
                        'Closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                        'Critical' => 'Critical',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Hardware' => 'Hardware',
                        'Software' => 'Software',
                        'Network' => 'Network',
                        'Account' => 'Account',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markInProgress')
                        ->label('→ In Progress')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn(IctTicket $record) => $record->update(['status' => 'In Progress']))
                        ->visible(fn(IctTicket $record): bool => $record->status === 'Open'),

                    Tables\Actions\Action::make('markResolved')
                        ->label('→ Resolved')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(IctTicket $record) => $record->update(['status' => 'Resolved']))
                        ->visible(fn(IctTicket $record): bool => in_array($record->status, ['Open', 'In Progress'])),

                    Tables\Actions\Action::make('markClosed')
                        ->label('→ Closed')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(fn(IctTicket $record) => $record->update(['status' => 'Closed']))
                        ->visible(fn(IctTicket $record): bool => $record->status === 'Resolved'),
                ])->label('Update Status')
                    ->icon('heroicon-o-arrow-path'),
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
            'index' => Pages\ListIctTickets::route('/'),
            'create' => Pages\CreateIctTicket::route('/create'),
            'view' => Pages\ViewIctTicket::route('/{record}'),
            'edit' => Pages\EditIctTicket::route('/{record}/edit'),
        ];
    }
}