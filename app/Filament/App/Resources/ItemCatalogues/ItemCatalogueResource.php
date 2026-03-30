<?php

namespace App\Filament\App\Resources\ItemCatalogues;

use App\Filament\App\Resources\ItemCatalogues\Pages\CreateItemCatalogue;
use App\Filament\App\Resources\ItemCatalogues\Pages\EditItemCatalogue;
use App\Filament\App\Resources\ItemCatalogues\Pages\ListItemCatalogues;
use App\Filament\App\Resources\ItemCatalogues\Pages\ViewItemCatalogue;
use App\Filament\App\Resources\ItemCatalogues\Schemas\ItemCatalogueForm;
use App\Filament\App\Resources\ItemCatalogues\Schemas\ItemCatalogueInfolist;
use App\Filament\App\Resources\ItemCatalogues\Tables\ItemCataloguesTable;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemCatalogueResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Item Catalogue';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ItemCatalogueForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemCatalogueInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemCataloguesTable::configure($table);
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
            'index' => ListItemCatalogues::route('/'),
            // 'create' => CreateItemCatalogue::route('/create'),
            'view' => ViewItemCatalogue::route('/{record}'),
            // 'edit' => EditItemCatalogue::route('/{record}/edit'),
        ];
    }
}
