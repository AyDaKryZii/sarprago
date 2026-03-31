<?php

namespace App\Actions;

use App\Events\ActivityLogged;
use App\Helpers\LogHelper;
use App\Models\ActivityLog;
use App\Models\Fine;
use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\LoanDetail;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\DB;

class LoanAction
{
    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->modalHeading('Confirm Approval')
            ->visible(fn (Loan $record) => in_array($record->status, ['pending', 'partially_approved']))
            ->fillForm(fn (Loan $record) => [
                'items' => $record->loanItems->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->item->name,
                    'qty_request' => $item->qty_request,
                    'qty_approved' => $item->qty_request,
                ])->toArray(),
            ])
            ->form([
                Repeater::make('items')
                    ->schema([
                        Hidden::make('id'),
                        TextInput::make('name')->label('Nama Barang')->disabled(),
                        TextInput::make('qty_request')->label('Request')->numeric()->disabled(),
                        TextInput::make('qty_approved')
                            ->label('Disetujui')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(fn ($get) => $get('qty_request')),
                    ])->addable(false)->deletable(false)->columns(4),
            ])
            ->action(function (Loan $record, array $data) {
                DB::transaction(function () use ($record, $data) {
                    $isPartial = false;
                    foreach ($data['items'] as $itemData) {
                        $loanItem = LoanItem::find($itemData['id']);
                        if ($itemData['qty_approved'] < $loanItem->qty_request) $isPartial = true;
                        $loanItem->update(['qty_approved' => $itemData['qty_approved']]);
                    }
                    $record->update([
                        'status' => $isPartial ? 'partially_approved' : 'approved',
                        'approved_by' => auth()->id(),
                    ]);
                });
                Notification::make()->title('Loan successfully approved')->success()->send();
            })
            ->after(fn (Loan $record) =>
                event(new ActivityLogged(
                    $record,
                    "Approved {$record->loan_code}",
                    'Transaction',
                    LogHelper::format($record, ['status', 'approved_by']),
                ))
            );
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-m-x-circle')
            ->color('danger')
            ->form([
                Textarea::make('admin_note')->label('Reason for Rejection')->required(),
            ])
            ->visible(fn (Loan $record) => $record->status === 'pending')
            ->action(function (Loan $record, array $data) {
                $record->update(['status' => 'rejected', 'admin_note' => $data['admin_note']]);
                Notification::make()->title('Loan Rejected')->danger()->send();
            })
            ->after(fn (Loan $record) =>
                event(new ActivityLogged(
                    $record,
                    "Rejected {$record->loan_code}",
                    'Transaction',
                    LogHelper::format($record, ['admin_note']),
            )));
    }

    public static function startLoan(): Action
    {
        return Action::make('startLoan')
            ->label('Hand Over')
            ->icon('heroicon-m-hand-raised')
            ->color('primary')
            ->modalHeading('Hand Over')
            ->modalDescription('Make sure you have selected the correct unit for each item.')
            ->modalSubmitActionLabel('Start Loan')
            ->visible(fn (Loan $record) => $record->status === 'approved')
            ->fillForm(fn (Loan $record) => [
                'assign_mode' => 'auto', 
                'items' => $record->loanItems->map(fn ($loanItem) => [
                    'id' => $loanItem->id,
                    'item_id' => $loanItem->item_id,
                    'name' => $loanItem->item->name,
                    'qty_approved' => $loanItem->qty_approved,
                    'unit_ids' => [],
                ])->toArray(),
            ])
            ->form([
                Select::make('assign_mode')
                    ->label('Metode Penyerahan')
                    ->options(['auto' => 'Otomatis (Sistem)', 'manual' => 'Pilih Unit Manual'])
                    ->live()
                    ->native(false),
                Repeater::make('items')
                    ->schema([
                        Hidden::make('id'),
                        Hidden::make('item_id'),
                        TextInput::make('name')
                            ->label('Item')
                            ->disabled(),
                        TextInput::make('qty_approved')
                            ->label('Quantity Approved')
                            ->dehydrated()
                            ->disabled(),
                        Select::make('unit_ids')
                            ->label('Choose Unit')
                            ->multiple()
                            ->searchable()
                            ->options(fn ($get) => 
                                ItemUnit::where('item_id', $get('item_id'))
                                    ->whereIn('status', ['available', 'reserved'])
                                    ->pluck('unit_code', 'id')
                            )
                            ->visible(fn ($get) => $get('../../assign_mode') === 'manual')
                            ->required(fn ($get) => $get('../../assign_mode') === 'manual')
                            ->rules([
                                fn ($get): \Closure => function ($attribute, $value, $fail) use ($get) {
                                    if (count($value) != $get('qty_approved')) {
                                        $fail("You must choose exaclty {$get('qty_approved')} unit.");
                                    }
                                },
                            ]),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->hiddenLabel(),
            ])
            ->action(function (Loan $record, array $data) {
                DB::transaction(function () use ($record, $data) {
                    foreach ($data['items'] as $itemData) {
                        $loanItem = LoanItem::find($itemData['id']);

                        if ($data['assign_mode'] === 'manual') {
                            foreach ($loanItem->loanDetails as $detail) {
                                $detail->itemUnit->update(['status' => 'available']);
                                $detail->delete();
                            }

                            $unitIds = $itemData['unit_ids'];

                            if (count($unitIds) < $itemData['qty_approved']) {
                                throw new \Exception("Failed Handover: Unit not enough.");
                            }

                            foreach ($unitIds as $unitId) {
                                $loanItem->loanDetails()->create([
                                    'item_unit_id' => $unitId,
                                ]);
                                ItemUnit::where('id', $unitId)->update(['status' => 'borrowed']);
                            }

                        } else {
                            $details = $loanItem->loanDetails;

                            if ($details->count() < $itemData['qty_approved']) {
                                throw new \Exception("Failed Handover: There is no unit reserved.");
                            }

                            foreach ($details as $detail) {
                                $detail->itemUnit->update(['status' => 'borrowed']);
                            }
                        }
                    }

                    $record->update([
                        'status' => 'on_going',
                        'borrowed_at' => now(),
                    ]);
                });
                Notification::make()->title('Items Successfully Handed Over')->success()->send();
            })
            ->after(fn (Loan $record) =>
                event(new ActivityLogged(
                    $record,
                    "Handed Over Items from Loan: {$record->loan_code} to: {$record->user->name}",
                    'Transaction',
                )));
    }

    public static function finishLoan(): Action
    {
        return Action::make('finishLoan')
            ->label('Finish Loan')
            ->icon('heroicon-m-check-badge')
            ->color('success')
            ->modalHeading('Finalize Loan')
            ->requiresConfirmation()
            ->visible(function (Loan $record) {
                $stillBorrowed = $record->loanItems()
                    ->whereHas('loanDetails', fn($q) => $q->whereNull('returned_at'))
                    ->exists();

                return $record->status === 'on_going' && !$stillBorrowed;
            })
            ->form([
                Section::make('Fine Section')
                    ->description('Fill this section if you want to add fine to this loan.')
                    ->schema([
                        TextInput::make('fine_amount')
                            ->label('Fine Amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Textarea::make('fine_reason')
                            ->label('Fine Reason')
                            ->placeholder('Example: Late return, Item damaged, etc.')
                            ->required(fn ($get) => $get('fine_amount') > 0),
                    ]),
            ])
            ->action(function (Loan $record, array $data) {
                DB::transaction(function () use ($record, $data) {
                    if ($data['fine_amount'] > 0) {
                        $fine = Fine::create([
                            'loan_id' => $record->id,
                            'user_id' => $record->user_id,
                            'amount' => $data['fine_amount'],
                            'reason' => $data['fine_reason'],
                            'status' => 'unpaid',
                        ]);

                        event(new ActivityLogged(
                            $fine,
                            "Added new fine: {$fine->fine_amount} (ID: {$fine->id}) from: {$fine->loan->loan_code} to: {$fine->user->name}",
                            'Transaction',
                        ));
                    }

                    $record->update([
                        'status' => 'finished',
                        'finished_at' => now(), 
                    ]);
                });

                Notification::make()
                    ->title('Loan has been finished')
                    ->success()
                    ->send();
            })
            ->after(fn (Loan $record) =>
                event(new ActivityLogged(
                    $record,
                    "Finished Loan: {$record->loan_code}",
                    'Transaction',
                )));
    }
}