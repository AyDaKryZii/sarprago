<?php

namespace App\Filament\Admin\Resources\Fines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('loan.id')
                    ->label('Loan'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('reason'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('paid_at')
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
