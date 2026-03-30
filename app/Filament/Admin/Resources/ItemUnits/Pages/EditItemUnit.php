<?php

namespace App\Filament\Admin\Resources\ItemUnits\Pages;

use App\Filament\Admin\Resources\ItemUnits\ItemUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditItemUnit extends EditRecord
{
    protected static string $resource = ItemUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
