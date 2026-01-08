<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Leave Requests';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Employee Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->label('Position')
                            ->maxLength(255),
                        Forms\Components\Select::make('department')
                            ->label('Department')
                            ->options([
                                'KB/TK' => 'KB/TK',
                                'SD' => 'SD',
                                'SMP' => 'SMP',
                                'PKBM' => 'PKBM',
                                'ICT' => 'ICT',
                                'HRD' => 'HRD',
                                'Finance & Accounting' => 'Finance & Accounting',
                                'Marketing' => 'Marketing',
                                'Management' => 'Management',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Leave Period')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->afterOrEqual('start_date'),
                        Forms\Components\TextInput::make('request_days')
                            ->label('Days Requested')
                            ->numeric()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Leave Details')
                    ->schema([
                        Forms\Components\Textarea::make('purpose')
                            ->label('Purpose of Leave')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('note')
                            ->label('Additional Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Hak Cuti')
                    ->schema([
                        Forms\Components\Placeholder::make('hak_prev_display')
                            ->label('Hak Cuti Tahun Sebelumnya')
                            ->content(function ($record) {
                                if (!$record || !$record->created_by)
                                    return '0 days';
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                return $quota ? number_format($quota->previous_year_quota, 1) . ' days' : '0 days';
                            }),

                        Forms\Components\Placeholder::make('hak_curr_display')
                            ->label('Hak Cuti Tahun Berjalan')
                            ->content(function ($record) {
                                if (!$record || !$record->created_by)
                                    return '12 days';
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                return $quota ? number_format($quota->current_year_quota, 1) . ' days' : '12 days';
                            }),

                        Forms\Components\Placeholder::make('total_hak_display')
                            ->label('Total Hak Cuti')
                            ->content(function ($record) {
                                if (!$record || !$record->created_by)
                                    return '12 days';
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                return $quota ? number_format($quota->total_quota, 1) . ' days' : '12 days';
                            }),

                        Forms\Components\Placeholder::make('quota_used_display')
                            ->label('Cuti yang Sudah Diambil')
                            ->content(function ($record) {
                                if (!$record || !$record->created_by)
                                    return '0 days';
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                return $quota ? number_format($quota->quota_used, 1) . ' days' : '0 days';
                            }),

                        Forms\Components\Placeholder::make('remaining_display')
                            ->label('Sisa Hak Cuti')
                            ->content(function ($record) {
                                if (!$record || !$record->created_by)
                                    return '12 days';
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                if (!$quota)
                                    return '12 days';

                                $remaining = $quota->remaining_quota;
                                $class = $remaining < 1 ? 'text-danger-600 font-bold' : 'text-success-600 font-semibold';
                                return new \Illuminate\Support\HtmlString("<span class='{$class}'>" . number_format($remaining, 1) . " days</span>");
                            })
                            ->helperText(function ($record) {
                                if (!$record || !$record->created_by)
                                    return null;
                                $quota = \App\Models\LeaveQuota::where('user_id', Auth::id() ?? $record->created_by)
                                    ->where('quota_year', date('Y'))
                                    ->first();
                                if ($quota && $quota->remaining_quota < 1) {
                                    return '⚠️ Kuota cuti tidak mencukupi!';
                                }
                                return null;
                            }),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Pending' => 'Pending Approval',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Draft')
                            ->required(),
                        Forms\Components\TextInput::make('current_approval_step')
                            ->label('Current Step')
                            ->numeric()
                            ->default(0)
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_days')
                    ->label('Days')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Draft',
                        'warning' => 'Pending',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('department')
                    ->options([
                        'KB/TK' => 'KB/TK',
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'ICT' => 'ICT',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(\App\Models\LeaveRequest $record) => route('leave.pdf', $record))
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
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'view' => Pages\ViewLeaveRequest::route('/{record}'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
