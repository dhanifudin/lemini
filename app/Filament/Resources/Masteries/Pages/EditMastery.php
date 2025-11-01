<?php

namespace App\Filament\Resources\Masteries\Pages;

use App\Filament\Resources\Masteries\MasteryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMastery extends EditRecord
{
    protected static string $resource = MasteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
