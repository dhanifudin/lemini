<?php

namespace App\Filament\Resources\Masteries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasteryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('objective_code')
                    ->required(),
                TextInput::make('level')
                    ->required(),
                DateTimePicker::make('last_seen_at')
                    ->required(),
            ]);
    }
}
