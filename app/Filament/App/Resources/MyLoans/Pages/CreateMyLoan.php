<?php

namespace App\Filament\App\Resources\MyLoans\Pages;

use App\Filament\App\Resources\MyLoans\MyLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyLoan extends CreateRecord
{
    protected static string $resource = MyLoanResource::class;
}
