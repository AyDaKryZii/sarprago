<?php

namespace App\Filament\Admin\Resources\Fines\Tables;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan.loan_code')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
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
                Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Payment')
                    ->modalDescription('Are you sure the user has paid this fine?')
                    ->visible(fn ($record) => $record->status === 'unpaid') 
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Fine marked as paid')
                            ->success()
                            ->send();
                    })
                    ->after(fn ($record) =>
                        event(new ActivityLogged(
                            $record,
                            "Fine for: {$record->loan->loan_code} has been paid",
                            'Transaction',
                            LogHelper::format($record, ['paid_at']),
                    ))),
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
