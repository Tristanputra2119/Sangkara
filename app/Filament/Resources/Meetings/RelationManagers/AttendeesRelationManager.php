<?php

namespace App\Filament\Resources\Meetings\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excuse' => 'Excuse',
                    ])
                    ->required()
                    ->default('present'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excuse' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'excuse' => 'Excuse',
                            ])
                            ->required()
                            ->default('present'),
                    ]),
                Action::make('addAllMembers')
                    ->label('Add All Members')
                    ->icon('heroicon-o-users')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire): void {
                        $meeting = $livewire->getOwnerRecord();
                        $existingUserIds = $meeting->attendees()->pluck('users.id')->toArray();
                        
                        $usersToAdd = User::whereNotIn('id', $existingUserIds)->get();
                        
                        $attachData = [];
                        foreach ($usersToAdd as $user) {
                            $attachData[$user->id] = ['status' => 'present'];
                        }
                        
                        if (!empty($attachData)) {
                            $meeting->attendees()->attach($attachData);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
