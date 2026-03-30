<?php

namespace App\Actions;

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
            });
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
            });
    }

public static function startLoan(): Action
{
    return Action::make('startLoan')
        ->label('Hand Over')
        ->icon('heroicon-m-hand-raised')
        ->color('primary')
        // ->requiresConfirmation() // HAPUS INI agar modal input muncul duluan
        ->modalHeading('Serah Terima Barang')
        ->modalDescription('Pastikan unit yang dipilih sudah sesuai sebelum memproses status On Going.')
        ->modalSubmitActionLabel('Proses Hand Over') // Mengganti label tombol submit
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
                        ->label('Barang')
                        ->disabled(),
                        
                    TextInput::make('qty_approved')
                        ->label('Jumlah')
                        ->dehydrated()
                        ->disabled(),

                    Select::make('unit_ids')
                        ->label('Pilih Unit Spesifik')
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
                                    $fail("Harus memilih tepat {$get('qty_approved')} unit.");
                                }
                            },
                        ]),
                ])
                ->addable(false)
                ->deletable(false)
                ->disableLabel(),
        ])
->action(function (Loan $record, array $data) {
    DB::transaction(function () use ($record, $data) {
        foreach ($data['items'] as $itemData) {
            $loanItem = LoanItem::find($itemData['id']);

            if ($data['assign_mode'] === 'manual') {
                // Reset hanya untuk mode manual (user mau pilih unit baru)
                foreach ($loanItem->loanDetails as $detail) {
                    $detail->itemUnit->update(['status' => 'available']);
                    $detail->delete();
                }

                $unitIds = $itemData['unit_ids'];

                if (count($unitIds) < $itemData['qty_approved']) {
                    throw new \Exception("Gagal Handover: Unit tidak cukup.");
                }

                foreach ($unitIds as $unitId) {
                    $loanItem->loanDetails()->create([
                        'item_unit_id' => $unitId,
                        'condition_out' => ItemUnit::find($unitId)->condition ?? 'good',
                    ]);
                    ItemUnit::where('id', $unitId)->update(['status' => 'borrowed']);
                }

            } else {
                // Mode auto: LoanDetail sudah ada dari Observer, tinggal update status unit
                $details = $loanItem->loanDetails;

                if ($details->count() < $itemData['qty_approved']) {
                    throw new \Exception("Gagal Handover: Unit reserved tidak ditemukan.");
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

    Notification::make()->title('Barang berhasil diserahkan')->success()->send();
});
}

    public static function finishLoan(): Action
{
    return Action::make('finishLoan')
        ->label('Selesaikan Peminjaman')
        ->icon('heroicon-m-check-badge')
        ->color('success')
        ->modalHeading('Finalisasi & Input Denda (Jika Ada)')
        ->requiresConfirmation()
        // HANYA MUNCUL jika status on_going DAN semua barang sudah fisik kembali (returned_at tidak null)
        ->visible(function (Loan $record) {
            $stillBorrowed = $record->loanItems()
                ->whereHas('loanDetails', fn($q) => $q->whereNull('returned_at'))
                ->exists();

            return $record->status === 'on_going' && !$stillBorrowed;
        })
        ->form([
            Section::make('Informasi Denda')
                ->description('Isi jika ada keterlambatan atau kerusakan barang.')
                ->schema([
                    TextInput::make('fine_amount')
                        ->label('Nominal Denda')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                    Textarea::make('fine_reason')
                        ->label('Alasan Denda')
                        ->placeholder('Contoh: Terlambat 2 hari & 1 unit rusak')
                        ->required(fn ($get) => $get('fine_amount') > 0),
                ]),
        ])
        ->action(function (Loan $record, array $data) {
            \DB::transaction(function () use ($record, $data) {
                // 1. Buat record denda jika nominal > 0
                if ($data['fine_amount'] > 0) {
                    \App\Models\Fine::create([
                        'loan_id' => $record->id,
                        'user_id' => $record->user_id,
                        'amount' => $data['fine_amount'],
                        'reason' => $data['fine_reason'],
                        'status' => 'unpaid',
                    ]);
                }

                // 2. Update status Loan menjadi finished
                $record->update([
                    'status' => 'returned',
                    'returned_at' => now(), // Timestamp finalisasi admin
                ]);
            });

            Notification::make()
                ->title('Peminjaman telah diselesaikan')
                ->success()
                ->send();
        });
}}