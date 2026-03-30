<?php

namespace App\Filament\Admin\Resources\ItemUnits\Pages;

use App\Filament\Admin\Resources\ItemUnits\ItemUnitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItemUnit extends ViewRecord
{
    protected static string $resource = ItemUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
