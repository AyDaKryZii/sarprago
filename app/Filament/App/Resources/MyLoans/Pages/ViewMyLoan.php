<?php

namespace App\Filament\App\Resources\MyLoans\Pages;

use App\Filament\App\Resources\MyLoans\MyLoanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMyLoan extends ViewRecord
{
    protected static string $resource = MyLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
