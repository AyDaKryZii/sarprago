<?php

namespace App\Filament\Admin\Resources\LoanItems\Pages;

use App\Filament\Admin\Resources\LoanItems\LoanItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoanItems extends ListRecords
{
    protected static string $resource = LoanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
