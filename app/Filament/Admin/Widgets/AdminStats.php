<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            // 1. Peminjaman yang masih Pending
            Stat::make('Pending Loans', Loan::where('status', 'pending')->count())
                ->description('Need to be approved')
                ->descriptionIcon(Heroicon::Clock)
                ->color('warning')
                ->url(route('filament.admin.resources.loans.index', [
                    'tableFilters[status][value]' => 'pending' 
                ])),

            // 2. Total Barang yang Available
            // Asumsinya ada kolom 'status' di table barang/items
            Stat::make('Available Units', ItemUnit::where('status', 'available')->count())
                ->description('Ready to be borrowed')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('success')
                ->url(route('filament.admin.resources.item-units.index', [
                    'tableFilters[status][value]' => 'available'
                ])),

            // 3. Total User Terdaftar
            Stat::make('Total User', User::count())
                ->description('All registered users')
                ->descriptionIcon(Heroicon::Users)
                ->color('info')
                ->url(route('filament.admin.resources.users.index')),
        ];
    }
}
