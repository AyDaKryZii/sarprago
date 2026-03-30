<?php

namespace App\Filament\Admin\Resources\Categories\RelationManagers;

use App\Filament\Admin\Resources\Items\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'Items';

    protected static ?string $relatedResource = ItemResource::class;

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
