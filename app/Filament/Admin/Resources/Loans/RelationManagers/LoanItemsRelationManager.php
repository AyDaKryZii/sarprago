<?php

namespace App\Filament\Admin\Resources\Loans\RelationManagers;

use App\Filament\Admin\Resources\LoanItems\LoanItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LoanItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'LoanItems';

    protected static ?string $relatedResource = LoanItemResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
