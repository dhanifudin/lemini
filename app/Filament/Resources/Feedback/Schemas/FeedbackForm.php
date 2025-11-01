<?php

namespace App\Filament\Resources\Feedback\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FeedbackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('attempt_id')
                    ->relationship('attempt', 'id')
                    ->required(),
                Textarea::make('ai_text')
                    ->columnSpanFull(),
                Textarea::make('human_revision')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required(),
                DateTimePicker::make('released_at'),
            ]);
    }
}
