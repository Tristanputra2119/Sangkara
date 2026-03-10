<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->helperText('Upload foto profil (format: jpg, png, gif). Maksimal 2MB.')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->circleCropper()
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->placeholder('Contoh: Budi Santoso')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->placeholder('Contoh: budi@sangkara.com')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->disabled(fn (string $operation, $record): bool => 
                        $operation === 'edit' && $record?->isOAuthUser()
                    )
                    ->helperText(fn (string $operation, $record): string => 
                        $operation === 'edit' && $record?->isOAuthUser() 
                            ? 'Kata sandi tidak dapat diubah untuk akun OAuth2 (Google login).'
                            : 'Kosongkan jika tidak ingin mengubah kata sandi (pada saat edit).'
                    ),
                Select::make('roles')
                    ->label('Peran (Role)')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->helperText('Pilih hak akses untuk pengguna ini (bisa lebih dari satu).'),
            ]);
    }
}
