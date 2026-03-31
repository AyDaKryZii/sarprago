<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Actions\LoanAction;
use App\Actions\LoanActions;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan_code')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->tooltip(fn ($record) => $record->user->email),
                TextColumn::make('approvedBy.name')
                    ->searchable()
                    ->label('Approved By')
                    ->tooltip(fn ($record) => $record->approvedBy?->email)
                    ->placeholder('N/A'),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (Loan $record): string => match (true) {
                        $record->status === 'borrowed' && $record->due_at?->isPast() => 'danger', // overdue    
                        $record->status === 'pending' => 'warning',
                        $record->status === 'approved' => 'success',
                        $record->status === 'partially_approved' => 'info',
                        $record->status === 'borrowed' => 'primary',
                        $record->status === 'finished' => 'success',
                        $record->status === 'rejected', $record->status === 'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (Loan $record): string => match (true) {
                        $record->status === 'borrowed' && $record->due_at?->isPast() => 'heroicon-m-exclamation-triangle',
                        $record->status === 'pending' => 'heroicon-m-clock',
                        $record->status === 'borrowed' => 'heroicon-m-hand-raised',
                        $record->status === 'finished' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-information-circle',
                    })
                    ->formatStateUsing(fn (Loan $record): string => 
                        ($record->status === 'borrowed' && $record->due_at?->isPast()) 
                            ? 'Overdue' 
                            : str($record->status)->replace('_', ' ')->title()
                    ),
                TextColumn::make('borrowed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finished_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                self::statusFilter(),
            ])
            ->headerActions([
                Action::make('exportPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('info')
                    ->url(route('loan.report.summary'))
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                
                LoanAction::approve(),
                LoanAction::reject(),
                LoanAction::startLoan(),
                LoanAction::finishLoan(),

                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn ($record) => $record->status === 'finished'),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function statusFilter()
    {
        return Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('status', 'borrowed')
                        ->where('due_at', '<', now())
                    )
                    ->indicator('Overdue');
    }
}
