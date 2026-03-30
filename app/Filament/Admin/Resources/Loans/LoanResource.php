<?php

namespace App\Filament\Admin\Resources\Loans;

use App\Filament\Admin\Resources\Loans\Pages\CreateLoan;
use App\Filament\Admin\Resources\Loans\Pages\EditLoan;
use App\Filament\Admin\Resources\Loans\Pages\ListLoans;
use App\Filament\Admin\Resources\Loans\Pages\ViewLoan;
use App\Filament\Admin\Resources\Loans\RelationManagers\LoanItemsRelationManager;
use App\Filament\Admin\Resources\Loans\Schemas\LoanForm;
use App\Filament\Admin\Resources\Loans\Schemas\LoanInfolist;
use App\Filament\Admin\Resources\Loans\Tables\LoansTable;
use App\Models\Loan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Loan Management';

    protected static ?string $recordTitleAttribute = 'loan_code';

    public static function form(Schema $schema): Schema
    {
        return LoanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LoanItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoans::route('/'),
            'create' => CreateLoan::route('/create'),
            'view' => ViewLoan::route('/{record}'),
            'edit' => EditLoan::route('/{record}/edit'),
        ];
    }
}
