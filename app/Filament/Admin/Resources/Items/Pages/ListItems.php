<?php

namespace App\Filament\Admin\Resources\Items\Pages;

use App\Filament\Admin\Resources\Items\ItemResource;
use App\Filament\Imports\ItemImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()->importer(ItemImporter::class),
        ];
    }
}
