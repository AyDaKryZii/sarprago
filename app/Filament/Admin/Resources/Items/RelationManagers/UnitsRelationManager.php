<?php

namespace App\Filament\Admin\Resources\Items\RelationManagers;

use App\Filament\Admin\Resources\ItemUnits\ItemUnitResource;
use App\Models\ItemUnit;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'Units';

    protected static ?string $relatedResource = ItemUnitResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('createBulkUnits')
                    ->label('Add Unit')
                    ->icon(Heroicon::Plus)
                    ->form([
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
                ->action(function (array $data, $livewire) {
                    $item = $livewire->getOwnerRecord();

                    for ($i = 1; $i <= $data['qty']; $i++) {
                        $item->units()->create([
                            'attributes' => $data['attributes']
                        ]);
                    }
                }),
            ]);
    }
}
