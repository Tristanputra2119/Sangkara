<?php

namespace App\Filament\Resources\Proposals\Tables;

use App\Models\Proposal;
use App\Services\ProposalGeneratorService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProposalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Pengajuan')
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
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'       => 'Draft',
                        'processing'  => 'Diproses',
                        'approved'    => 'Disetujui',
                        'rejected'    => 'Ditolak',
                        default       => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'processing'  => 'warning',
                        'approved'    => 'success',
                        'rejected'    => 'danger',
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
                Action::make('downloadDocument')
                    ->label('Unduh Dokumen')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Proposal $record) {
                        // Generate the DOCX file first
                        $service = app(\App\Services\ProposalGeneratorService::class);
                        $relativePath = $service->generate($record);
                        $fullDocxPath = storage_path('app/public/' . $relativePath);

                        // Just download the DOCX
                        return response()->download($fullDocxPath)->deleteFileAfterSend(false);
                    })
                    ->visible(fn (Proposal $record) => !empty($record->content)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengajuan')
            ->emptyStateDescription('Mulai dengan membuat pengajuan kegiatan baru.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
