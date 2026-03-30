<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                        $record->status === 'borrowed' && $record->due_at?->isPast() => 'danger',
                        $record->status === 'pending' => 'warning',
                        $record->status === 'approved' => 'success',
                        $record->status === 'partially_approved' => 'info',
                        $record->status === 'borrowed' => 'primary',
                        $record->status === 'returned' => 'success',
                        $record->status === 'rejected', $record->status === 'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (Loan $record): string => match (true) {
                        $record->status === 'borrowed' && $record->due_at?->isPast() => 'heroicon-m-exclamation-triangle',
                        $record->status === 'pending' => 'heroicon-m-clock',
                        $record->status === 'borrowed' => 'heroicon-m-hand-raised',
                        $record->status === 'returned' => 'heroicon-m-check-circle',
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
                TextColumn::make('returned_at')
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
Action::make('approve')
    ->label('Approve / Partial')
    ->icon('heroicon-m-check-circle')
    ->color('success')
    ->modalHeading('Konfirmasi Persetujuan')
    ->visible(fn (Loan $record) => in_array($record->status, ['pending', 'partially_approved']))
    ->fillForm(fn (Loan $record) => [
        'items' => $record->loanItems->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->item->name,
            'qty_request' => $item->qty_request,
            'qty_approved' => $item->qty_approved ?? $item->qty_request, // Default samakan dengan request
        ])->toArray(),
    ])
    ->form([
        Repeater::make('items')
            ->label('Daftar Barang yang Diminta')
            ->schema([
                Hidden::make('id'),
                TextInput::make('name')
                    ->label('Nama Barang')
                    ->disabled(),
                TextInput::make('qty_request')
                    ->label('Request')
                    ->numeric()
                    ->disabled(),
                TextInput::make('qty_approved')
                    ->label('Disetujui')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(fn ($get) => $get('qty_request')) // Tidak boleh lebih dari request
                    ->helperText('Ubah jika hanya ingin menyetujui sebagian.'),
            ])
            ->addable(false)
            ->deletable(false)
            ->columns(4),
    ])
    ->action(function (Loan $record, array $data) {
        \DB::transaction(function () use ($record, $data) {
            $isPartial = false;

            foreach ($data['items'] as $itemData) {
                $loanItem = \App\Models\LoanItem::find($itemData['id']);
                
                // Cek apakah ada jumlah yang dikurangi admin
                if ($itemData['qty_approved'] < $loanItem->qty_request) {
                    $isPartial = true;
                }

                $loanItem->update(['qty_approved' => $itemData['qty_approved']]);
            }

            // Tentukan status berdasarkan apakah ada pemotongan jumlah
            $newStatus = $isPartial ? 'partially_approved' : 'approved';

            $record->update([
                'status' => $newStatus,
                'approved_by' => auth()->id(),
            ]);
        });

        Notification::make()->title('Persetujuan berhasil diproses.')->success()->send();
    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan alasan penolakan...')
                            ->required(),
                    ])
                    ->modalHeading('Reject Loan')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->visible(fn (Loan $record) => $record->status === 'pending')
                    ->action(function (Loan $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_note' => $data['admin_note'],
                        ]);

                        Notification::make()
                            ->title('Loan Rejected')
                            ->body("Loan {$record->loan_code} berhasil di-reject.")
                            ->danger()
                            ->send();
                    }),
                Action::make('startLoan')
    ->label('Serahkan Barang')
    ->icon('heroicon-m-hand-raised')
    ->color('primary')
    ->requiresConfirmation()
    ->modalHeading('Konfirmasi Penyerahan Barang')
    ->modalDescription('Pastikan user sudah menerima barang secara fisik sebelum menekan tombol ini.')
    ->modalSubmitActionLabel('Ya, Barang Sudah Diserahkan')
    // Tombol ini hanya muncul jika status sudah 'approved' 
    // (setelah user klik "Terima" di sisi mereka)
    ->visible(fn (Loan $record) => $record->status === 'approved')
    ->action(function (Loan $record) {
        \DB::transaction(function () use ($record) {
            foreach ($record->loanItems as $item) {
                // Ubah status unit yang tersisa (yang sudah disetujui) dari reserved -> borrowed
                $item->loanDetails->each(function ($detail) {
                    $detail->itemUnit->update(['status' => 'borrowed']);
                });
            }

            // Update status Loan utama
            $record->update([
                'status' => 'borrowed',
                'borrowed_at' => now(), // Mencatat waktu peminjaman dimulai
            ]);
        });

        Notification::make()
            ->title('Peminjaman Aktif')
            ->body("Barang pada loan {$record->loan_code} resmi dipinjam.")
            ->success()
            ->send();
    }),
Action::make('returnLoan')
    ->label('Kembalikan Barang')
    ->icon('heroicon-m-arrow-path-rounded-square')
    ->color('success')
    ->modalHeading('Proses Pengembalian Barang')
    // Hanya muncul jika barang sedang dipinjam
    ->visible(fn (Loan $record) => $record->status === 'borrowed')
    ->fillForm(fn (Loan $record) => [
        'items' => $record->loanItems->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->item->name,
            'qty_approved' => $item->qty_approved,
            // Hitung unit yang saat ini statusnya masih 'borrowed'
            'qty_still_borrowed' => $item->loanDetails()
                ->whereHas('itemUnit', fn($q) => $q->where('status', 'borrowed'))
                ->count(),
            'qty_to_return' => 0,
        ])->toArray(),
    ])
    ->form([
        Repeater::make('items')
            ->label('Item di Tangan User')
            ->schema([
                Hidden::make('id'),
                TextInput::make('name')->label('Barang')->disabled(),
                TextInput::make('qty_still_borrowed')
                    ->label('Sisa Pinjam')
                    ->disabled()
                    ->extraAttributes(['class' => 'font-bold text-danger-600']),
                TextInput::make('qty_to_return')
                    ->label('Jumlah Kembali')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(fn ($get) => $get('qty_still_borrowed'))
                    ->required(),
            ])
            ->addable(false)
            ->deletable(false)
            ->columns(4),
    ])
    ->action(function (Loan $record, array $data) {
        \DB::transaction(function () use ($record, $data) {
            foreach ($data['items'] as $itemData) {
                $qtyToReturn = (int) $itemData['qty_to_return'];

                if ($qtyToReturn > 0) {
                    $loanItem = \App\Models\LoanItem::find($itemData['id']);
                    
                    // Ambil unit yang masih 'borrowed', lalu balikkan ke 'available'
                    $details = $loanItem->loanDetails()
                        ->whereHas('itemUnit', fn($q) => $q->where('status', 'borrowed'))
                        ->limit($qtyToReturn)
                        ->get();

                    foreach ($details as $detail) {
                        $detail->itemUnit->update(['status' => 'available']);
                    }
                }
            }

            // CEK AKHIR: Apakah SEMUA item untuk Loan ini sudah kembali ke gudang?
            // Kita hitung apakah masih ada unit berstatus 'borrowed' di Loan ini
            $stillBorrowedTotal = \App\Models\LoanDetail::whereHas('loanItem', fn($q) => $q->where('loan_id', $record->id))
                ->whereHas('itemUnit', fn($q) => $q->where('status', 'borrowed'))
                ->count();

            // Jika sudah 0, baru ubah status Loan jadi Returned
            if ($stillBorrowedTotal === 0) {
                $record->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                ]);
                
                Notification::make()->title('Peminjaman Selesai (Semua barang kembali)')->success()->send();
            } else {
                Notification::make()->title('Pengembalian parsial dicatat. Status tetap Borrowed.')->info()->send();
            }
        });
    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function approveActions()
    {
        return Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (Loan $record) => $record->status === 'pending')
            ->action(function (Loan $record) {
                $record->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);

                $record->loanItems()->update(['qty_approved' => \DB::raw('qty_request')]);

                Notification::make()->title('Loan Approved! Menunggu user mengambil barang.')->success()->send();
            });
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
