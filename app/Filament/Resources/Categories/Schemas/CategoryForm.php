<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->placeholder('Contoh: Konsumsi, Akomodasi')
                    ->helperText('Nama kategori yang digunakan untuk pengelompokan transaksi.')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Jenis Kategori')
                    ->helperText('Pilih apakah kategori ini untuk pemasukan atau pengeluaran.')
                    ->options([
                        'income'  => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ])
                    ->required(),
            ]);
    }
}
