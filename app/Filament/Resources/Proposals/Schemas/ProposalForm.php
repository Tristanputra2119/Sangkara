<?php

namespace App\Filament\Resources\Proposals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                TextInput::make('title')
                    ->label('Judul Pengajuan')
                    ->placeholder('Contoh: Pengajuan Dana Acara Seminar Nasional')
                    ->helperText('Masukkan judul pengajuan secara singkat dan jelas.')
                    ->required()
                    ->maxLength(255),
                Select::make('month')
                    ->label('Rencana Bulan')
                    ->helperText('Pilih bulan rencana pelaksanaan kegiatan.')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ])
                    ->required(),
                TextInput::make('year')
                    ->label('Tahun')
                    ->placeholder('Contoh: 2025')
                    ->helperText('Masukkan tahun rencana pelaksanaan (4 digit).')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\Section::make('Rencana Kegiatan')
                    ->description('Tambahkan detail rencana kegiatan yang akan diajukan.')
                    ->statePath('content')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kegiatan')
                            ->placeholder('Contoh: Seminar Pembukaan')
                            ->required(),
                        DatePicker::make('date')
                            ->label('Tanggal Pelaksanaan')
                            ->required(),
                        TextInput::make('location')
                            ->label('Lokasi Kegiatan')
                            ->placeholder('Contoh: Aula Kampus')
                            ->required(),
                        RichEditor::make('description')
                            ->label('Deskripsi Kegiatan')
                            ->helperText('Uraikan detail rencana kegiatan.')
                            ->toolbarButtons([
                                'attachFiles', 'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList', 'blockquote',
                                'h2', 'h3', 'undo', 'redo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('proposal-images')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status Pengajuan')
                    ->helperText('Status dokumen pengajuan saat ini.')
                    ->options([
                        'draft'      => 'Draft',
                        'processing' => 'Sedang Diproses',
                        'approved'   => 'Disetujui',
                        'rejected'   => 'Ditolak',
                    ])
                    ->default('draft')
                    ->visibleOn('edit'),
                TextInput::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->helperText('Diisi jika status pengajuan ditolak.')
                    ->visibleOn('edit')
                    ->columnSpanFull(),
                TextInput::make('file_path')
                    ->label('Path Dokumen')
                    ->disabled()
                    ->visibleOn('edit')
                    ->columnSpanFull(),
            ]);
    }
}
