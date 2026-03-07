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
                    ->label('Judul Rapat')
                    ->placeholder('Contoh: Rapat Perencanaan Acara Tahunan')
                    ->helperText('Masukkan judul atau nama rapat yang akan dilaksanakan.')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('meeting_date')
                    ->label('Tanggal & Waktu Rapat')
                    ->helperText('Pilih tanggal dan jam pelaksanaan rapat.')
                    ->required(),
                TextInput::make('meeting_link')
                    ->label('Link Rapat (Opsional)')
                    ->placeholder('Contoh: https://meet.google.com/xxx')
                    ->helperText('Isi link meeting online jika rapat dilakukan secara virtual.')
                    ->maxLength(255),
                Textarea::make('agenda')
                    ->label('Agenda Rapat')
                    ->placeholder('Tuliskan poin-poin agenda rapat...')
                    ->helperText('Daftar topik atau agenda yang akan dibahas dalam rapat.')
                    ->columnSpanFull(),
                RichEditor::make('minutes_content')
                    ->label('Notulensi Rapat')
                    ->helperText('Rekap hasil pembahasan dan keputusan yang diambil dalam rapat.')
                    ->toolbarButtons([
                        'attachFiles', 'bold', 'italic', 'underline',
                        'bulletList', 'orderedList', 'link', 'undo', 'redo',
                    ])
                    ->fileAttachmentsDisk('cloudinary')
                    ->fileAttachmentsDirectory('meeting-files')
                    ->columnSpanFull(),
                KeyValue::make('metadata')
                    ->label('Data Tambahan (Opsional)')
                    ->helperText('Tambahkan informasi tambahan dalam format pasangan kunci-nilai jika diperlukan.')
                    ->columnSpanFull(),
            ]);
    }
}
