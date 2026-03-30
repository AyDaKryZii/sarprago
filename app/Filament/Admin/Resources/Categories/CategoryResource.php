<?php

namespace App\Filament\Admin\Resources\Categories;

use App\Filament\Admin\Resources\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Categories\Pages\ListCategories;
use App\Filament\Admin\Resources\Categories\Pages\ViewCategory;
use App\Filament\Admin\Resources\Categories\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Admin\Resources\Categories\Schemas\CategoryInfolist;
use App\Filament\Admin\Resources\Categories\Tables\CategoriesTable;
use App\HasRolePermission;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    use HasRolePermission;

    protected static array $allowedroles = ['admin'];

    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static string|UnitEnum|null $navigationGroup = 'Item Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'view' => ViewCategory::route('/{record}'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
