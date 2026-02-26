<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                    ->required()
                    ->maxLength(255),
                Select::make('month')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ])
                    ->required(),
                TextInput::make('year')
                    ->numeric()
                    ->required(),
                Repeater::make('content')
                    ->label('Daftar Kegiatan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kegiatan')
                            ->required(),
                        DatePicker::make('date')
                            ->label('Tanggal Pelaksanaan')
                            ->required(),
                        TextInput::make('location')
                            ->label('Lokasi Kegiatan')
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi Kegiatan')
                            ->rows(3),
                    ])
                    ->grid(2)
                    ->collapsible()
                    ->defaultItems(1)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'processing' => 'Processing',
                        'finalized' => 'Finalized',
                        'failed' => 'Failed',
                    ])
                    ->default('draft')
                    ->visibleOn('edit'),
                TextInput::make('file_path')
                    ->disabled()
                    ->maxLength(255)
                    ->visibleOn('edit'),
            ]);
    }
}
