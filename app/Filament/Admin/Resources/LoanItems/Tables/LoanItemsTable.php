<?php

namespace App\Filament\Admin\Resources\LoanItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoanItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan.loan_code')
                    ->searchable(),
                TextColumn::make('item.name')
                    ->searchable(),
                TextColumn::make('qty_request')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('qty_approved')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->loan->status === 'pending' ? 'N/A' : $record->qty_approved),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
