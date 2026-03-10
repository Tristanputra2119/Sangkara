<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Foto')
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
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope'),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengguna')
            ->emptyStateDescription('Mulai dengan menambahkan anggota tim baru.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
