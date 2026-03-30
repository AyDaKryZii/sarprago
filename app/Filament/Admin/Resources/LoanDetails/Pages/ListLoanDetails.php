<?php

namespace App\Filament\Admin\Resources\LoanDetails\Pages;

use App\Filament\Admin\Resources\LoanDetails\LoanDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoanDetails extends ListRecords
{
    protected static string $resource = LoanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
