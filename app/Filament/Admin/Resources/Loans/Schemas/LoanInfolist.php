<?php

namespace App\Filament\Admin\Resources\Loans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('loan_code'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('approved_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('borrowed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('due_at')
                    ->dateTime(),
                TextEntry::make('returned_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('admin_note')
                    ->placeholder('-')
                    ->hidden(fn ($record) => $record->status !== 'rejected')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
