<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Loan;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestLoan extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
                ->recordUrl(fn ($record): string => route('filament.admin.resources.loans.view', $record))
                ->query(fn (): Builder => Loan::query())
                ->columns([
                    TextColumn::make('loan_code')
                        ->label('Code')
                        ->searchable(),
                TextColumn::make('user.name')
                    ->label('Borrower')
                    ->tooltip(fn ($record): ?string => $record->user->email),
                TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('N/A')
                    ->tooltip(fn ($record): ?string => $record->approver->email)
                    ->searchable(),
                TextColumn::make('borrowed_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('due_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('returned_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge(),
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
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
