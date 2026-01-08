<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequisitionResource\Pages;
use App\Filament\Resources\PurchaseRequisitionResource\RelationManagers;
use App\Models\PurchaseRequisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PurchaseRequisitionResource extends Resource
{
    protected static ?string $model = PurchaseRequisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Purchase Requisitions';

    protected static ?string $modelLabel = 'Purchase Requisition';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📝 Informasi Umum')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul/Deskripsi PR')
                            ->placeholder('Contoh: Pembelian Alat Kantor')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('department')
                            ->label('Departemen/Unit')
                            ->options([
                                'KB/TK' => 'KB/TK',
                                'SD' => 'SD',
                                'SMP' => 'SMP',
                                'PKBM' => 'PKBM',
                                'Customer Service Officer' => 'Customer Service Officer',
                                'Finance & Accounting' => 'Finance & Accounting',
                                'HRD' => 'HRD',
                                'ICT' => 'ICT',
                                'Management' => 'Management',
                                'Marketing' => 'Marketing',
                                'Operator' => 'Operator',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('requester')
                            ->label('Nama Pemohon')
                            ->default(fn() => Auth::user()->username ?? Auth::user()->name ?? '')
                            ->required(),

                        Forms\Components\DatePicker::make('needed_date')
                            ->label('Tanggal Permintaan Barang')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('budget_status')
                            ->label('Status Anggaran')
                            ->options([
                                'Dianggarkan' => 'Dianggarkan',
                                'Belum Dianggarkan' => 'Belum Dianggarkan',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan/Keterangan')
                            ->placeholder('Informasi tambahan atau penjelasan khusus...')
                            ->rows(3)
                            ->columnSpanFull(),

                        // Hidden Auto-generated fields
                        Forms\Components\Hidden::make('pr_number')
                            ->default(fn() => 'PR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                        Forms\Components\Hidden::make('status')
                            ->default('Draft'),
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => Auth::id()),
                    ])->columns(2),

                Forms\Components\Section::make('📦 Item yang Dibutuhkan')
                    ->description('Daftar barang atau jasa yang ingin diajukan')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('item_name')
                                    ->label('Nama Item')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit')
                                    ->label('Unit')
                                    ->placeholder('Pcs/Box/Kg')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->columnSpan(2),
                            ])
                            ->columns(6)
                            ->addActionLabel('+ Tambah Item')
                            ->defaultItems(1)
                            ->reorderableWithButtons()
                    ]),

                Forms\Components\Section::make('📄 Dokumen Pendukung')
                    ->description('Upload dokumen penawaran harga, brosur, dll (Max 5MB/file)')
                    ->schema([
                        Forms\Components\Repeater::make('documents')
                            ->relationship('documents')
                            ->schema([
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('File Dokumen')
                                    ->disk('public')
                                    ->directory('pr_documents')
                                    ->required()
                                    ->storeFileNamesIn('file_name'),
                            ])
                            ->grid(2)
                            ->addActionLabel('+ Upload Dokumen')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pr_number')
                    ->label('No. PR')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul PR')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester')
                    ->label('Pemohon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departemen')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('needed_date')
                    ->label('Tgl Dibutuhkan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Draft',
                        'warning' => 'Pending',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Departemen')
                    ->options([
                        'KB/TK' => 'KB/TK',
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'PKBM' => 'PKBM',
                        'Customer Service Officer' => 'Customer Service Officer',
                        'Finance & Accounting' => 'Finance & Accounting',
                        'HRD' => 'HRD',
                        'ICT' => 'ICT',
                        'Management' => 'Management',
                        'Marketing' => 'Marketing',
                        'Operator' => 'Operator',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print PDF')
                    ->icon('heroicon-o-printer')
                    ->url(fn(PurchaseRequisition $record) => route('pr.pdf', $record))
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
                // Relations are now handled in the main form, but we can keep Approvals here if separated.
                // Or remove Items/Documents relation managers since they are in the form now.
            RelationManagers\ApprovalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseRequisitions::route('/'),
            'create' => Pages\CreatePurchaseRequisition::route('/create'),
            'view' => Pages\ViewPurchaseRequisition::route('/{record}'),
            'edit' => Pages\EditPurchaseRequisition::route('/{record}/edit'),
        ];
    }
}
