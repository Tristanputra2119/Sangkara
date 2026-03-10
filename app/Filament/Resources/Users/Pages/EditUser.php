<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->label('Hapus Akun')
                ->modalHeading('Hapus Akun Pengguna')
                ->modalDescription('Apakah Anda yakin ingin menghapus akun ini? Semua data terkait akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus Akun')
                ->requiresConfirmation()
                ->successNotificationTitle('Akun berhasil dihapus'),
        ];
    }
}
