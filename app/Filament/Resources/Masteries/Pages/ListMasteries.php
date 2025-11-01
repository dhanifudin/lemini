<?php

namespace App\Filament\Resources\Masteries\Pages;

use App\Filament\Resources\Masteries\MasteryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasteries extends ListRecords
{
    protected static string $resource = MasteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
