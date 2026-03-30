<?php

namespace App\Filament\Admin\Resources\LoanDetails\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('loanItem.item.name')
                    ->label('Loan item'),
                TextEntry::make('itemUnit.unit_code')
                    ->label('Item unit'),
                TextEntry::make('condition_out')
                    ->badge(),
                TextEntry::make('condition_in')
                    ->badge()
                    ->placeholder('-'),
                ImageEntry::make('image_path')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        if ($record->image_path) {
                            return $record->image_path;
                        }

                        return $record->loanitem->item->image_path;
                    })
                    ->placeholder('No Image'),
                KeyValueEntry::make('attributes'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('returned_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
