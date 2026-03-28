<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Report;
use App\Services\ProposalGeneratorService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Laporan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(fn (int $state): string => [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ][$state] ?? $state)
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'       => 'Draft',
                        'processing'  => 'Diproses',
                        'finalized'   => 'Selesai',
                        'failed'      => 'Gagal',
                        default       => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'processing'  => 'warning',
                        'finalized'   => 'success',
                        'failed'      => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Edit'),
                Action::make('generateLpj')
                    ->label('Generate LPJ')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn (Report $record) => $record->generateDocx())
                    ->requiresConfirmation()
                    ->modalHeading('Generate Dokumen Word')
                    ->modalDescription('Dokumen LPJ dalam format Word akan digenerate di latar belakang. Lanjutkan?')
                    ->modalSubmitActionLabel('Ya, Generate')
                    ->disabled(fn (Report $record) => $record->status === 'processing'),
                Action::make('downloadLpj')
                    ->label('Unduh LPJ')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Report $record) {
                        if (empty($record->file_path)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File belum digenerate.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $fullDocxPath = storage_path('app/public/' . $record->file_path);
                        if (!file_exists($fullDocxPath)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File Word sumber tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        return response()->download($fullDocxPath)->deleteFileAfterSend(false);
                    })
                    ->visible(fn (Report $record) => $record->status === 'finalized' && !empty($record->file_path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Laporan')
            ->emptyStateDescription('Mulai dengan membuat laporan kegiatan pertama Anda.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
