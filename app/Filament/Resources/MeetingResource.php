<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingResource\Pages;
use App\Filament\Resources\MeetingResource\RelationManagers;
use App\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Absensi Meeting';

    protected static ?string $modelLabel = 'Meeting';

    protected static ?string $navigationGroup = 'Attendance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Meeting Details')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Kode Meeting')
                            ->default(fn() => Str::random(10))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Kode unik untuk QR absensi'),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Meeting')
                            ->options([
                                'internal' => 'Internal',
                                'external' => 'External',
                            ])
                            ->default('internal')
                            ->required()
                            ->helperText('Internal: Wajib login & 1x absen. External: Tamu bisa absen tanpa login.'),
                        Forms\Components\Textarea::make('title')
                            ->label('Judul Meeting')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode disalin'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('attendees_count')
                    ->label('Hadir')
                    ->counts('attendees')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('qrcode')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn(Meeting $record) => 'QR Code: ' . $record->title)
                    ->modalContent(fn(Meeting $record) => view('filament.meeting-qrcode', ['meeting' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                Tables\Actions\Action::make('print')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Meeting $record) => route('meeting.pdf', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Detail Meeting')
                    ->schema([
                        Components\TextEntry::make('id')
                            ->label('Kode Meeting')
                            ->copyable(),
                        Components\TextEntry::make('type')
                            ->label('Tipe')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'internal' => 'info',
                                'external' => 'warning',
                            }),
                        Components\TextEntry::make('title')
                            ->label('Judul'),
                        Components\TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),
                        Components\TextEntry::make('attendees_count')
                            ->label('Jumlah Hadir')
                            ->state(fn(Meeting $record) => $record->attendees()->count())
                            ->badge()
                            ->color('success'),
                    ])->columns(2),
                Components\Section::make('QR Code & Link')
                    ->schema([
                        Components\ViewEntry::make('qrcode')
                            ->label('QR Code')
                            ->view('filament.meeting-qrcode-infolist'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetings::route('/'),
            'create' => Pages\CreateMeeting::route('/create'),
            'view' => Pages\ViewMeeting::route('/{record}'),
            'edit' => Pages\EditMeeting::route('/{record}/edit'),
        ];
    }
}
