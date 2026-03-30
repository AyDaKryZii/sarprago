<?php

namespace App\Filament\Admin\Resources\Loans\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    DateTimePicker::make('due_at')
                        ->native(false)
                        ->required(),
                    Textarea::make('reason')
                        ->columnSpanFull(),
                ]),
                Repeater::make('loanItems') 
                    ->label('Loan Items')
                    ->relationship('loanItems')
                    ->itemLabel(function ($state) {
                        if (! isset($state['item_id'])) {
                            return 'New Item';
                        }

                        $item = Item::find($state['item_id']);

                        if (! $item) {
                            return null;
                        }

                        return $item->name . ' (x' . ($state['qty_request'] ?? 1) . ')';
                    })
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->relationship('item', 'name') 
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        TextInput::make('qty_request')
                            ->label('Qty Request')
                            ->numeric()
                            ->default(1)
                            ->live()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add Another Item')
                    ->collapsible()
                    ->defaultItems(1),
            ]); 
    }
}

