<?php

namespace App\Filament\App\Resources\ItemCatalogues\Pages;

use App\Filament\App\Resources\ItemCatalogues\ItemCatalogueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemCatalogues extends ListRecords
{
    protected static string $resource = ItemCatalogueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
