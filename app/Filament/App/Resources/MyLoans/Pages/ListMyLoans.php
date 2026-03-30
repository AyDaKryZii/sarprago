<?php

namespace App\Filament\App\Resources\MyLoans\Pages;

use App\Filament\App\Resources\MyLoans\MyLoanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyLoans extends ListRecords
{
    protected static string $resource = MyLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
