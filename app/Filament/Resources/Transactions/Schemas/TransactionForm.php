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
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                DatePicker::make('transaction_date')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('proof_path')
                    ->directory('transaction-proofs')
                    ->columnSpanFull(),
            ]);
    }
}
