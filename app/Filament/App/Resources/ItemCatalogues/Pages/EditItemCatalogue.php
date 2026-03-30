<?php

namespace App\Filament\App\Resources\ItemCatalogues\Pages;

use App\Filament\App\Resources\ItemCatalogues\ItemCatalogueResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditItemCatalogue extends EditRecord
{
    protected static string $resource = ItemCatalogueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
