<?php

namespace App\Filament\Resources\QuizSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuizSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('experiment_variant'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Textarea::make('settings')
                    ->columnSpanFull(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('submitted_at'),
            ]);
    }
}
