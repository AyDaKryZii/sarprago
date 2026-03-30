<?php

namespace App\Filament\Admin\Resources\ItemUnits\Pages;

use App\Filament\Admin\Resources\ItemUnits\ItemUnitResource;
use App\Models\ItemUnit;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListItemUnits extends ListRecords
{
    protected static string $resource = ItemUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBulkUnit')
                ->label('Add Unit')
                ->icon(Heroicon::Plus)
                ->form([
                    Select::make('item_id')
                        ->relationship('item', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('qty')
                        ->label('Unit Amount')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->maxValue(100)
                        ->required(),
                    KeyValue::make('attributes')
                        ->label('Additional Attributes')
                        ->keyLabel('Property')
                        ->valueLabel('Value')
                ])
                ->action(function (array $data) {
                    for ($i = 1; $i <= $data['qty']; $i++) {
                        ItemUnit::create([
                            'item_id' => $data['item_id'],
                            'attributes' => $data['attributes']
                        ]);
                    }
                }),
        ];
    }
}
