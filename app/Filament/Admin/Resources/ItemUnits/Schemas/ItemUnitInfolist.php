<?php

namespace App\Filament\Admin\Resources\ItemUnits\Schemas;

use App\Models\ItemUnit;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItemUnitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('item.name')
                    ->label('Item'),
                TextEntry::make('unit_code'),
                TextEntry::make('condition')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                ImageEntry::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->getStateUsing(function ($record) {
                        if ($record->image_path) {
                            return $record->image_path;
                        }

                        return $record->item?->image_path;
                    })
                    ->placeholder('No Image'),
                KeyValueEntry::make('attributes'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (ItemUnit $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
