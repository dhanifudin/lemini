<?php

namespace App\Filament\Resources\LearningObjectives\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LearningObjectiveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextInput::make('code')
                            ->label('Objective Code')
                            ->required()
                            ->unique(ignoreRecord: true, table: 'learning_objectives', column: 'code')
                            ->maxLength(50),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(150),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        TagsInput::make('standards')
                            ->label('Standards Alignment')
                            ->placeholder('Add standard code e.g. NGSS HS-LS1-7'),
                        TextInput::make('version')
                            ->default('v1')
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ]);
    }
}
