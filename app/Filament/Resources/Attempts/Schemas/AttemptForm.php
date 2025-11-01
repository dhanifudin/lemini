<?php

namespace App\Filament\Resources\Attempts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'stem')
                    ->required(),
                Textarea::make('response')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('score')
                    ->numeric(),
                Textarea::make('metadata')
                    ->columnSpanFull(),
            ]);
    }
}
