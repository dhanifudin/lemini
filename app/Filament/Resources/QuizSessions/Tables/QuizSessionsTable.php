<?php

namespace App\Filament\Resources\QuizSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuizSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.first_name')
                    ->label('Student')
                    ->formatStateUsing(fn ($record) => $record->user->first_name.' '.$record->user->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'submitted' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('average_score')
                    ->label('Score')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->suffix('%')
                    ->color(fn ($state) => match (true) {
                        $state >= 85 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('correct_count')
                    ->label('Correct')
                    ->sortable(),
                TextColumn::make('incorrect_count')
                    ->label('Incorrect')
                    ->sortable(),
                TextColumn::make('pending_review_count')
                    ->label('Pending Review')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('review.status')
                    ->label('Review Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'reviewed' => 'info',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->default('Not Reviewed'),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'submitted' => 'Submitted',
                    ]),
                SelectFilter::make('review_status')
                    ->label('Review Status')
                    ->relationship('review', 'status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'approved' => 'Approved',
                    ]),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
