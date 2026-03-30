<?php

namespace App\Filament\Admin\Resources\LoanDetails\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LoanDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('loan_item_id')
                    ->relationship('loanItem', 'id')
                    ->required(),
                Select::make('item_unit_id')
                    ->relationship('itemUnit', 'id')
                    ->required(),
                Select::make('condition_out')
                    ->options(['good' => 'Good', 'damaged' => 'Damaged'])
                    ->default('good')
                    ->required(),
                Select::make('condition_in')
                    ->options(['good' => 'Good', 'damaged' => 'Damaged', 'broken' => 'Broken']),
                DateTimePicker::make('returned_at'),
            ]);
    }
}
