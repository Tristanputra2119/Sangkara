<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                Select::make('category_id')
                    ->label('Kategori')
                    ->helperText('Pilih kategori yang sesuai untuk transaksi ini.')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->label('Jumlah (Rp)')
                    ->helperText('Masukkan nominal transaksi dalam Rupiah (tanpa titik atau koma).')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                DatePicker::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->helperText('Pilih tanggal transaksi ini terjadi.')
                    ->required(),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Pembelian banner untuk acara wisuda')
                    ->helperText('Tuliskan keterangan singkat tentang transaksi ini.')
                    ->columnSpanFull(),
                FileUpload::make('proof_path')
                    ->label('Bukti Transaksi (Foto/Struk)')
                    ->helperText('Upload foto struk atau bukti pembayaran (format: jpg, png, pdf).')
                    ->directory('transaction-proofs')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->columnSpanFull(),
            ]);
    }
}
