<?php

namespace App\Filament\Resources\Rubrics\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RubricForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('criteria')
                    ->required()
                    ->rows(5),
                Textarea::make('levels')
                    ->required()
                    ->rows(5),
            ]);
    }
}
