<?php

namespace App\Filament\Admin\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('username')
                    ->label('Causer')
                    ->searchable()
                    ->description(fn ($record) => "ID: #{$record->user_id}"),

                TextColumn::make('log_name')
                    ->label('Modul')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Security' => 'danger',
                        'Inventory' => 'warning',
                        'Category' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Action')
                    ->searchable()
                    ->wrap(), // Agar teks panjang tidak terpotong
            ])
            ->defaultSort('created_at', 'desc') // Log terbaru selalu di atas
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Filter Modul')
                    ->options([
                        'Security' => 'Security',
                        'Inventory' => 'Inventory',
                        'Category' => 'Category',
                    ]),
            ]);
    }
}
