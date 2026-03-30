<?php

namespace App\Filament\App\Resources\MyLoans\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
    ->modalHeading('Keputusan Persetujuan Sebagian')
    ->modalDescription('Admin hanya menyetujui sebagian barang. Silakan pilih untuk menerima jumlah tersebut atau batalkan semua.')
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
            ->label('Perbandingan Barang')
            ->schema([
                TextInput::make('name')->label('Barang')->disabled(),
                TextInput::make('qty_request')->label('Diminta')->disabled(),
                TextInput::make('qty_approved')->label('Disetujui Admin')->disabled()
                    ->extraAttributes(['class' => 'font-bold text-primary-600']),
            ])
            ->addable(false)
            ->deletable(false)
            ->columns(3),
    ])
    // Kita hilangkan tombol submit bawaan
    ->modalSubmitAction(false) 
    ->modalCancelAction(false)
    // Kita buat dua tombol kustom di footer modal
    ->extraModalActions([
// TOMBOL TERIMA
        Action::make('acceptAction')
            ->label('Terima & Lanjut')
            ->color('success')
            ->requiresConfirmation()
            ->action(function (Loan $record) {
                \DB::transaction(function () use ($record) {
                    foreach ($record->loanItems as $item) {
                        // Cek jika ada selisih antara yang di-reserve (qty_request) 
                        // dengan yang disetujui admin (qty_approved)
                        $diff = $item->qty_request - $item->qty_approved;

                        if ($diff > 0) {
                            // Ambil kelebihan unit yang statusnya masih reserved
                            $detailsToRelease = $item->loanDetails()
                                ->limit($diff)
                                ->get();

                            foreach ($detailsToRelease as $detail) {
                                // Kembalikan ke gudang (Available)
                                $detail->itemUnit->update(['status' => 'available']);
                                // Hapus detail transaksinya
                                $detail->delete();
                            }
                        }
                    }

                    // Update status utama menjadi approved (siap diambil)
                    $record->update(['status' => 'approved']);
                });

                Notification::make()->title('Keputusan Diterima')->success()->send();
            }),

        // TOMBOL BATALKAN
        Action::make('cancelAction')
            ->label('Batalkan Semua')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (Loan $record) {
                \DB::transaction(function () use ($record) {
                    foreach ($record->loanItems as $item) {
                        // Lepaskan SEMUA unit yang sudah di-reserve
                        $item->loanDetails->each(function ($detail) {
                            $detail->itemUnit->update(['status' => 'available']);
                        });
                        $item->loanDetails()->delete();
                    }
                    $record->update(['status' => 'cancelled']);
                });
                Notification::make()->title('Peminjaman Dibatalkan')->danger()->send();
            }),
            
        Action::make('close')->label('Nanti Saja')->color('gray')->close(),
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
                        \DB::transaction(function () use ($record) {
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
                    });
    }
}