<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                TextInput::make('title')
                    ->label('Judul Laporan')
                    ->placeholder('Contoh: Laporan Kegiatan OSPEK 2025')
                    ->helperText('Masukkan judul laporan yang singkat dan jelas.')
                    ->required()
                    ->maxLength(255),
                Select::make('month')
                    ->label('Bulan')
                    ->helperText('Pilih bulan pelaksanaan kegiatan.')
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
                    ->helperText('Masukkan tahun pelaksanaan laporan (4 digit).')
                    ->numeric()
                    ->required(),
                Repeater::make('content')
                    ->label('Daftar Kegiatan')
                    ->helperText('Tambahkan satu atau lebih kegiatan yang tercakup dalam laporan ini.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kegiatan')
                            ->placeholder('Contoh: Seminar Kepemimpinan')
                            ->helperText('Nama singkat dari kegiatan yang dilaksanakan.')
                            ->required(),
                        DatePicker::make('date')
                            ->label('Tanggal Pelaksanaan')
                            ->helperText('Pilih tanggal kegiatan berlangsung.')
                            ->required(),
                        TextInput::make('location')
                            ->label('Lokasi Kegiatan')
                            ->placeholder('Contoh: Aula Utama, Gedung A')
                            ->helperText('Tempat berlangsungnya kegiatan.')
                            ->required(),
                        RichEditor::make('description')
                            ->label('Deskripsi Kegiatan')
                            ->helperText('Uraikan detail kegiatan. Anda dapat menyisipkan gambar menggunakan tombol di toolbar.')
                            ->toolbarButtons([
                                'attachFiles',
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->fileAttachmentsDisk('cloudinary')
                            ->fileAttachmentsDirectory('kegiatan-images')
                            ->columnSpanFull(),
                    ])
                    ->grid(2)
                    ->collapsible()
                    ->defaultItems(1)
                    ->addActionLabel('+ Tambah Kegiatan')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status Laporan')
                    ->helperText('Status ini menunjukkan tahap dokumen laporan saat ini.')
                    ->options([
                        'draft'       => 'Draft',
                        'processing'  => 'Sedang Diproses',
                        'finalized'   => 'Selesai',
                        'failed'      => 'Gagal',
                    ])
                    ->default('draft')
                    ->visibleOn('edit'),
                TextInput::make('file_path')
                    ->label('Path File Dokumen')
                    ->helperText('Lokasi file dokumen yang sudah digenerate (diisi otomatis).')
                    ->disabled()
                    ->maxLength(255)
                    ->visibleOn('edit'),
            ]);
    }
}
