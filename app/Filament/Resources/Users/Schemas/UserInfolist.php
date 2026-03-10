<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('avatar')
                    ->label('Foto Profil')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        if ($record->avatar) {
                            // If it's a full URL (Cloudinary), return as is
                            if (str_starts_with($record->avatar, 'http://') || str_starts_with($record->avatar, 'https://')) {
                                return $record->avatar;
                            }
                            // Otherwise, it's a local storage path
                            return \Storage::url($record->avatar);
                        }
                        return null;
                    })
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('email')
                    ->label('Alamat Email'),
                TextEntry::make('oauth_provider')
                    ->label('Login Method')
                    ->badge()
                    ->color(fn (string $state = null): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state = null): string => 
                        $state ? 'OAuth (' . ucfirst($state) . ')' : 'Standard (Email/Password)'
                    ),
                TextEntry::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum terverifikasi'),
                TextEntry::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y, H:i'),
                TextEntry::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i'),
            ]);
    }
}
