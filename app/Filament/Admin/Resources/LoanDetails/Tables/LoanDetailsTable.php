<?php

namespace App\Filament\Admin\Resources\LoanDetails\Tables;

use App\Models\LoanDetail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class LoanDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loanItem.item.name')
                    ->searchable(),
                TextColumn::make('itemUnit.unit_code')
                    ->searchable(),
                TextColumn::make('condition_out')
                    ->badge(),
                TextColumn::make('condition_in')
                    ->badge(),
                TextColumn::make('returned_at')
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
                Action::make('receiveUnit')
                    ->label('Receive')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (LoanDetail $record) => $record->returned_at === null)
                    ->form([
                        Select::make('condition_in')
                            ->label('Item Condition')
                            ->options([
                                'good' => 'Good',
                                'damaged' => 'Damaged',
                                'broken' => 'Broken',
                                'lost' => 'Lost',
                            ])
                            ->default('good')
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (LoanDetail $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'condition_in' => $data['condition_in'],
                                'returned_at' => now(),
                            ]);

                            $newCondition = $data['condition_in'];
                            
                            $record->itemUnit->update([
                                'condition' => $newCondition,
                            ]);
                        });
                    }),
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
