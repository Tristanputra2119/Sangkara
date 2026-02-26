<?php

namespace App\Filament\Resources\Meetings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MeetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('meeting_date')
                    ->required(),
                TextInput::make('meeting_link')
                    ->maxLength(255),
                Textarea::make('agenda')
                    ->columnSpanFull(),
                RichEditor::make('minutes_content')
                    ->columnSpanFull(),
                KeyValue::make('metadata')
                    ->columnSpanFull(),
            ]);
    }
}
