<?php

namespace App\Filament\Admin\Resources\ItemUnits\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ItemUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required(),
                TextInput::make('unit_code')
                    ->required(),
                Select::make('condition')
                    ->options(['good' => 'Good', 'damaged' => 'Damaged', 'broken' => 'Broken'])
                    ->default('good')
                    ->required(),
                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                        'borrowed' => 'Borrowed',
                        'lost' => 'Lost',
                        'maintenance' => 'Maintenance',
                    ])
                    ->default('available')
                    ->required(),
                FileUpload::make('image_path')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->label('Image')
                    ->imageEditor(),
                KeyValue::make('attributes'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
