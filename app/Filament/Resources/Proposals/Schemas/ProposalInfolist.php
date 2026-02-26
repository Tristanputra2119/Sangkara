<?php

namespace App\Filament\Resources\Proposals\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProposalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('title'),
                TextEntry::make('file_path')
                    ->placeholder('-'),
                TextEntry::make('month')
                    ->numeric(),
                TextEntry::make('year')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('rejection_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
