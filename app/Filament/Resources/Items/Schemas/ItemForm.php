<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\LearningObjective;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rubric_id')
                    ->relationship('rubric', 'name')
                    ->required()
                    ->preload(),
                Select::make('learning_objective_id')
                    ->label('Learning Objective')
                    ->relationship('learningObjective', 'title')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, ?string $state): void {
                        if (! $state) {
                            return;
                        }

                        $objective = LearningObjective::query()->find($state);

                        if ($objective) {
                            $set('objective_code', $objective->code);
                        }
                    }),
                TextInput::make('objective_code')
                    ->label('Objective Code')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->maxLength(50),
                Textarea::make('stem')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'MCQ' => 'Multiple Choice',
                        'SAQ' => 'Short Answer',
                    ])
                    ->required(),
                Textarea::make('options')
                    ->rows(5),
                Textarea::make('answer')
                    ->required()
                    ->rows(3),
                Textarea::make('rationale')
                    ->rows(3),
                Textarea::make('meta')
                    ->rows(3),
            ]);
    }
}
