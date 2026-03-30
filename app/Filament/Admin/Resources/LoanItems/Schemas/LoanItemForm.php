<?php

namespace App\Filament\Admin\Resources\LoanItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LoanItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('loan_id')
                    ->relationship('loan', 'id')
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required(),
                TextInput::make('qty_request')
                    ->required()
                    ->numeric(),
                TextInput::make('qty_approved')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
