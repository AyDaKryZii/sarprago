<?php

namespace App\Filament\App\Resources\MyLoans\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MyLoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peminjaman')
                ->schema([
                    TextEntry::make('id')->label('ID Pinjam'),
                    TextEntry::make('created_at')->label('Waktu Pinjam')->dateTime(),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('due_at')->label('Tenggat Kembali')->date(),
                    TextEntry::make('reason')->label('Alasan')->columnSpanFull(),
                ]),

            Section::make('Daftar Barang')
                ->schema([
                    RepeatableEntry::make('loanItems') // Nama relasi di model Loan
                        ->label('')
                        ->schema([
                            TextEntry::make('item.name') // Relasi loanItem ke Item
                                ->label('Nama Barang'),
                            TextEntry::make('qty_request')
                                ->label('Jumlah Diminta'),
                            // Opsional: Tampilkan kode unit jika sudah disetujui
                            TextEntry::make('loanDetails.itemUnit.unit_code')
                                ->label('Kode Unit')
                                ->listWithLineBreaks()
                                ->bulleted(),
                        ])
                        ->columns(2),
                ]),
        ]);
    }
}
