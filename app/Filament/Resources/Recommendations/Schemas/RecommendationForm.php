<?php

namespace App\Filament\Resources\Recommendations\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Textarea::make('payload')
                    ->required()
                    ->columnSpanFull(),
                Checkbox::make('chosen'),
            ]);
    }
}
