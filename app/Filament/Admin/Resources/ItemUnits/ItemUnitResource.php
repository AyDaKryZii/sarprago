<?php

namespace App\Filament\Admin\Resources\ItemUnits;

use App\Filament\Admin\Resources\ItemUnits\Pages\CreateItemUnit;
use App\Filament\Admin\Resources\ItemUnits\Pages\EditItemUnit;
use App\Filament\Admin\Resources\ItemUnits\Pages\ListItemUnits;
use App\Filament\Admin\Resources\ItemUnits\Pages\ViewItemUnit;
use App\Filament\Admin\Resources\ItemUnits\Schemas\ItemUnitForm;
use App\Filament\Admin\Resources\ItemUnits\Schemas\ItemUnitInfolist;
use App\Filament\Admin\Resources\ItemUnits\Tables\ItemUnitsTable;
use App\HasRolePermission;
use App\Models\ItemUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ItemUnitResource extends Resource
{
    use HasRolePermission;

    protected static array $allowedroles = ['admin'];

    protected static ?string $model = ItemUnit::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'unit_code';

    public static function form(Schema $schema): Schema
    {
        return ItemUnitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemUnitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemUnitsTable::configure($table);
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
            'index' => ListItemUnits::route('/'),
            'create' => CreateItemUnit::route('/create'),
            'view' => ViewItemUnit::route('/{record}'),
            'edit' => EditItemUnit::route('/{record}/edit'),
        ];
    }
}
