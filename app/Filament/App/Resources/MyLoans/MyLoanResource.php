<?php

namespace App\Filament\App\Resources\MyLoans;

use App\Filament\App\Resources\MyLoans\Pages\CreateMyLoan;
use App\Filament\App\Resources\MyLoans\Pages\EditMyLoan;
use App\Filament\App\Resources\MyLoans\Pages\ListMyLoans;
use App\Filament\App\Resources\MyLoans\Pages\ViewMyLoan;
use App\Filament\App\Resources\MyLoans\Schemas\MyLoanForm;
use App\Filament\App\Resources\MyLoans\Schemas\MyLoanInfolist;
use App\Filament\App\Resources\MyLoans\Tables\MyLoansTable;
use App\Models\Loan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MyLoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'loan_code';

    protected static ?string $navigationLabel = 'My Loans';

    public static function form(Schema $schema): Schema
    {
        return MyLoanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyLoanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyLoansTable::configure($table);
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
            'index' => ListMyLoans::route('/'),
            'create' => CreateMyLoan::route('/create'),
            'view' => ViewMyLoan::route('/{record}'),
            'edit' => EditMyLoan::route('/{record}/edit'),
        ];
    }
}
