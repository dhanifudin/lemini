<?php

namespace App\Filament\Resources\Masteries;

use App\Filament\Resources\Masteries\Pages\CreateMastery;
use App\Filament\Resources\Masteries\Pages\EditMastery;
use App\Filament\Resources\Masteries\Pages\ListMasteries;
use App\Filament\Resources\Masteries\Schemas\MasteryForm;
use App\Filament\Resources\Masteries\Tables\MasteriesTable;
use App\Models\Mastery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasteryResource extends Resource
{
    protected static ?string $model = Mastery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MasteryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasteriesTable::configure($table);
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
            'index' => ListMasteries::route('/'),
            'create' => CreateMastery::route('/create'),
            'edit' => EditMastery::route('/{record}/edit'),
        ];
    }
}
