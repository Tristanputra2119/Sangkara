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
                    ->searchable(),
                TextColumn::make('month')
                    ->formatStateUsing(fn (int $state): string => [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ][$state] ?? $state)
                    ->sortable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'processing' => 'warning',
                        'finalized' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('generateLpj')
                    ->label('Generate LPJ')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn (Report $record) => $record->generateDocx())
                    ->requiresConfirmation()
                    ->disabled(fn (Report $record) => $record->status === 'processing'),
                Action::make('downloadPengajuan')
                    ->label('Download Pengajuan')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Report $record) {
                        $service = app(ProposalGeneratorService::class);
                        $filePath = $service->generate($record);
                        $fullPath = storage_path('app/public/' . $filePath);

                        return response()->download($fullPath)->deleteFileAfterSend(false);
                    })
                    ->requiresConfirmation()
                    ->disabled(fn (Report $record) => empty($record->content)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
