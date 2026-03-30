<?php

namespace App\Filament\App\Resources\ItemCatalogues\Pages;

use App\Filament\App\Resources\ItemCatalogues\ItemCatalogueResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItemCatalogue extends ViewRecord
{
    protected static string $resource = ItemCatalogueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
