<?php

namespace App\Filament\Admin\Resources\LoanItems;

use App\Filament\Admin\Resources\LoanItems\Pages\CreateLoanItem;
use App\Filament\Admin\Resources\LoanItems\Pages\EditLoanItem;
use App\Filament\Admin\Resources\LoanItems\Pages\ListLoanItems;
use App\Filament\Admin\Resources\LoanItems\Pages\ViewLoanItem;
use App\Filament\Admin\Resources\LoanItems\RelationManagers\LoanDetailsRelationManager;
use App\Filament\Admin\Resources\LoanItems\Schemas\LoanItemForm;
use App\Filament\Admin\Resources\LoanItems\Schemas\LoanItemInfolist;
use App\Filament\Admin\Resources\LoanItems\Tables\LoanItemsTable;
use App\HasRolePermission;
use App\Models\LoanItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class LoanItemResource extends Resource
{
    use HasRolePermission;

    protected static array $allowedroles = ['admin', 'staff'];

    protected static ?string $model = LoanItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return LoanItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoanItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LoanDetailsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanItems::route('/'),
            'create' => CreateLoanItem::route('/create'),
            'view' => ViewLoanItem::route('/{record}'),
            'edit' => EditLoanItem::route('/{record}/edit'),
        ];
    }
}
