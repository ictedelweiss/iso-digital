<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Storage;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        TextInput::make('ms_email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('display_name')
                            ->label('Display Name')
                            ->placeholder('Nama lengkap untuk tampilan')
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('division')
                            ->label('Divisi / Departemen')
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
                            ->searchable(),
                    ]),

                Section::make('Digital Signature')
                    ->description('Tanda tangan ini akan digunakan untuk absensi meeting dan approval dokumen.')
                    ->schema([
                        ViewField::make('signature_data')
                            ->label('Signature')
                            ->view('filament.forms.components.signature-pad')
                            ->default(fn() => $this->getUser()->signature_path ? Storage::url($this->getUser()->signature_path) : null)
                            ->dehydrated(false), // Handled in mutate
                    ]),

                $this->getPasswordFormComponent(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Get the signature data from the request directly since ViewField might behave differently in hydration
        // Actually, we need to bind the state of signature-pad to a field. 
        // Let's assume the component will update the state of 'signature_data'.

        $signatureData = $data['signature_data'] ?? null;

        // Check if it's base64 (new signature)
        if ($signatureData && str_starts_with($signatureData, 'data:image')) {
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $filename = 'signatures/user_' . auth()->id() . '_' . time() . '.png';

            Storage::disk('public')->put($filename, base64_decode($image));

            $data['signature_path'] = $filename;
        }

        // Remove the temporary validation field
        unset($data['signature_data']);

        return $data;
    }


    protected function getRedirectUrl(): ?string
    {
        return filament()->getPanel()->getUrl();
    }
}
