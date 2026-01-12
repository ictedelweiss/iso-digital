<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveQuotaResource\Pages;
use App\Models\LeaveQuota;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;

class LeaveQuotaResource extends Resource
{
    protected static ?string $model = LeaveQuota::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Leave Quotas';

    protected static ?string $modelLabel = 'Leave Quota';

    protected static ?string $pluralModelLabel = 'Leave Quotas';

    protected static ?string $navigationGroup = 'HRD Management';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Employee')
                            ->options(function () {
                                return \App\Models\User::all()->mapWithKeys(function ($user) {
                                    // Convert username to proper display name
                                    // e.g., "ketut.dewi.laksmi" -> "Ketut Dewi Laksmi"
                                    $displayName = ucwords(str_replace('.', ' ', $user->username));
                                    return [$user->id => $displayName];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?int $state) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        // Set employee_name as proper display name
                                        $displayName = ucwords(str_replace('.', ' ', $user->username));
                                        $set('employee_name', $displayName);
                                    }
                                }
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\User::where('username', 'like', "%{$search}%")
                                    ->orWhere('ms_email', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        $displayName = ucwords(str_replace('.', ' ', $user->username));
                                        return [$user->id => $displayName];
                                    });
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $user = \App\Models\User::find($value);
                                if ($user) {
                                    return ucwords(str_replace('.', ' ', $user->username));
                                }
                                return null;
                            }),

                        Forms\Components\TextInput::make('employee_name')
                            ->label('Employee Name')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('department')
                            ->label('Department')
                            ->options(config('approval.departments', [
                                'KB/TK',
                                'SD',
                                'SMP',
                                'PKBM',
                                'ICT',
                                'HRD',
                                'Finance & Accounting',
                                'Marketing',
                                'Management',
                                'Operator',
                                'Customer Service Officer',
                            ]))
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Quota Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('quota_year')
                            ->label('Quota Year')
                            ->required()
                            ->numeric()
                            ->default(date('Y'))
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->suffix('Year'),

                        Forms\Components\TextInput::make('previous_year_quota')
                            ->label('Hak Cuti Tahun Sebelumnya')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('days')
                            ->helperText('Sisa cuti dari tahun sebelumnya yang belum diambil'),

                        Forms\Components\TextInput::make('current_year_quota')
                            ->label('Hak Cuti Tahun Berjalan')
                            ->required()
                            ->numeric()
                            ->default(12)
                            ->minValue(0)
                            ->suffix('days')
                            ->helperText('Hak cuti untuk tahun ini (default: 12 hari)'),

                        Forms\Components\Placeholder::make('total_quota_display')
                            ->label('Total Hak Cuti')
                            ->content(fn($record) => $record ? number_format($record->total_quota, 1) . ' days' : '12.0 days'),

                        Forms\Components\TextInput::make('quota_used')
                            ->label('Cuti yang Sudah Diambil')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->suffix('days')
                            ->helperText('Otomatis terisi saat cuti disetujui'),

                        Forms\Components\Placeholder::make('remaining_display')
                            ->label('Sisa Hak Cuti')
                            ->content(fn($record) => $record ? number_format($record->remaining_quota, 1) . ' days' : '12.0 days'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quota_year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('previous_year_quota')
                    ->label('Previous Year')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_year_quota')
                    ->label('Current Year')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_quota')
                    ->label('Total Quota')
                    ->state(fn($record) => $record->total_quota)
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' days')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('quota_used')
                    ->label('Used')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' days')
                    ->sortable()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('remaining_quota')
                    ->label('Remaining')
                    ->state(fn($record) => $record->remaining_quota)
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' days')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn($record) => $record->remaining_quota < 1 ? 'danger' : 'primary'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('employee_name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('quota_year')
                    ->label('Year')
                    ->options(function () {
                        $currentYear = (int) date('Y');
                        return [
                            $currentYear - 1 => $currentYear - 1,
                            $currentYear => $currentYear,
                            $currentYear + 1 => $currentYear + 1,
                        ];
                    })
                    ->default(date('Y')),

                Tables\Filters\SelectFilter::make('department')
                    ->options(config('approval.departments', [])),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveQuotas::route('/'),
            'create' => Pages\CreateLeaveQuota::route('/create'),
            'edit' => Pages\EditLeaveQuota::route('/{record}/edit'),
        ];
    }

    /**
     * Access control: Only HRD and Admin can access this resource
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Allow admin or HRD (Medina Marpaung)
        return $user->role === 'admin' ||
            $user->role === 'superadmin' ||
            strtolower($user->email) === 'medina.marpaung@edelweiss.sch.id';
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
