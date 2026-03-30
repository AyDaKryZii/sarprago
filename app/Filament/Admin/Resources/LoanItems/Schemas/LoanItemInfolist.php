<?php

namespace App\Filament\Admin\Resources\LoanItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('loan.loan_code')
                    ->label('Loan Code'),
                TextEntry::make('item.name')
                    ->label('Item'),
                TextEntry::make('qty_request')
                    ->formatStateUsing(fn ($record) => $record->qty_request - 1 === 0 ? $record->qty_request . ' Unit'  : $record->qty_request  . ' Units'),
                TextEntry::make('qty_approved')
                    ->formatStateUsing(fn ($record) => $record->loan->status === 'pending' ? 'N/A' : $record->qty_approved . 'Units'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
