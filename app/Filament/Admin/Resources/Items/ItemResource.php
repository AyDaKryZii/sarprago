<?php

namespace App\Filament\Admin\Resources\Items;

use App\Filament\Admin\Resources\Items\Pages\CreateItem;
use App\Filament\Admin\Resources\Items\Pages\EditItem;
use App\Filament\Admin\Resources\Items\Pages\ListItems;
use App\Filament\Admin\Resources\Items\Pages\ViewItem;
use App\Filament\Admin\Resources\Items\RelationManagers\UnitsRelationManager;
use App\Filament\Admin\Resources\Items\Schemas\ItemForm;
use App\Filament\Admin\Resources\Items\Schemas\ItemInfolist;
use App\Filament\Admin\Resources\Items\Tables\ItemsTable;
use App\HasRolePermission;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ItemResource extends Resource
{
    use HasRolePermission;

    protected static array $allowedroles = ['admin'];

    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Item Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UnitsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'view' => ViewItem::route('/{record}'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }
}
