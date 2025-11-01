<?php

namespace App\Filament\Resources\LearningObjectives;

use App\Filament\Resources\LearningObjectives\Pages\CreateLearningObjective;
use App\Filament\Resources\LearningObjectives\Pages\EditLearningObjective;
use App\Filament\Resources\LearningObjectives\Pages\ListLearningObjectives;
use App\Filament\Resources\LearningObjectives\Schemas\LearningObjectiveForm;
use App\Filament\Resources\LearningObjectives\Tables\LearningObjectivesTable;
use App\Models\LearningObjective;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LearningObjectiveResource extends Resource
{
    protected static ?string $model = LearningObjective::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LearningObjectiveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LearningObjectivesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLearningObjectives::route('/'),
            'create' => CreateLearningObjective::route('/create'),
            'edit' => EditLearningObjective::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
