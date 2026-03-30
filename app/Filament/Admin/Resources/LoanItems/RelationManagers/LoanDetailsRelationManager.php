<?php

namespace App\Filament\Admin\Resources\LoanItems\RelationManagers;

use App\Filament\Admin\Resources\LoanDetails\LoanDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LoanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'LoanDetails';

    protected static ?string $relatedResource = LoanDetailResource::class;

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
