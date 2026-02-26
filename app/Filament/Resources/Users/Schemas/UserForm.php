<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->helperText('Kosongkan jika tidak ingin mengubah kata sandi (pada saat edit).'),
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
