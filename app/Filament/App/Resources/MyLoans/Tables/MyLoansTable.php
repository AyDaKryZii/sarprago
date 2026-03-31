<?php

namespace App\Filament\App\Resources\MyLoans\Tables;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class MyLoansTable
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
                    ->getStateUsing(fn ($record) => $record->status_label)
                    ->color(fn (string $state): string => match ($state) {
                        'Overdue' => 'danger',
                        'Pending' => 'warning',
                        'Approved', 'Borrowed' => 'info',
                        'Borrowed (Partial Return)' => 'primary',
                        'Returned' => 'success',
                        'Rejected', 'Cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Overdue' => 'heroicon-m-exclamation-triangle',
                        'Borrowed' => 'heroicon-m-arrow-path',
                        'Returned' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-clock',
                    }),
                TextColumn::make('borrowed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('returned_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                self::cancelLoan(),
                Action::make('respondPartial')
                    ->label('Respon Keputusan')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('warning')
                    ->modalHeading('Respond Partial Approval')
                    ->modalDescription('Admin did not approve all items.')
                    ->visible(fn (Loan $record) => $record->status === 'partially_approved')
                    ->fillForm(fn (Loan $record) => [
                        'items' => $record->loanItems->map(fn ($item) => [
                            'name' => $item->item->name,
                            'qty_request' => $item->qty_request,
                            'qty_approved' => $item->qty_approved,
                        ])->toArray(),
                    ])
                    ->form([
                        Repeater::make('items')
                            ->label('Quantity Comparison')
                            ->schema([
                                TextInput::make('name')->label('Item')->disabled(),
                                TextInput::make('qty_request')->label('Qty Request')->disabled(),
                                TextInput::make('qty_approved')->label('Qty Approved')->disabled()
                                    ->extraAttributes(['class' => 'font-bold text-primary-600']),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->columns(3),
                    ])
                    ->modalSubmitAction(false) 
                    ->modalCancelAction(false)
                    ->extraModalFooterActions([
                        Action::make('acceptAction')
                            ->label('Accept')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(function (Loan $record) {
                                DB::transaction(function () use ($record) {
                                    foreach ($record->loanItems as $item) {
                                        $diff = $item->qty_request - $item->qty_approved;

                                        if ($diff > 0) {
                                            $detailsToRelease = $item->loanDetails()
                                                ->limit($diff)
                                                ->get();

                                            foreach ($detailsToRelease as $detail) {
                                                $detail->itemUnit->update(['status' => 'available']);
                                                $detail->delete();
                                            }
                                        }
                                    }

                                    $record->update(['status' => 'approved']);
                                });

                                Notification::make()->title('Loan Approved')->success()->send();
                            }),

                        Action::make('cancelAction')
                            ->label('Cancelled All')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function (Loan $record) {
                                DB::transaction(function () use ($record) {
                                    foreach ($record->loanItems as $item) {
                                        $item->loanDetails->each(function ($detail) {
                                            $detail->itemUnit->update(['status' => 'available']);
                                        });
                                        $item->loanDetails()->delete();
                                    }
                                    $record->update(['status' => 'cancelled']);
                                });
                                Notification::make()->title('Loan Cancelled')->danger()->send();
                            })
                            ->after(fn (Loan $record) =>
                                event(new ActivityLogged(
                                    $record,
                                    "Cancelled {$record->loan_code}",
                                    'Transaction',
                                    LogHelper::format($record, ['admin_note']),
                            ))),
                            
                        Action::make('close')->label('Later')->color('gray')->close(),
                    ])
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function cancelLoan()
    {
        return Action::make('cancelLoan')
                    ->label('Cancel Request')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel this request?')
                    ->visible(fn (Loan $record) => $record->status === 'pending')
                    ->action(function (Loan $record) {
                        DB::transaction(function () use ($record) {
                            foreach ($record->loanItems as $item) {
                                foreach ($item->loanDetails as $detail) {
                                    $detail->itemUnit->update(['status' => 'available']);
                                }

                                $item->loanDetails()->delete();
                            }

                            $record->update(['status' => 'cancelled']);
                        });

                        Notification::make()
                            ->title('Request Cancelled!')
                            ->success()
                            ->send();
                    })
                    ->after(fn (Loan $record) =>
                        event(new ActivityLogged(
                            $record,
                            "Cancelled {$record->loan_code}",
                            'Transaction',
                            LogHelper::format($record, ['admin_note']),
                    )));
    }
}