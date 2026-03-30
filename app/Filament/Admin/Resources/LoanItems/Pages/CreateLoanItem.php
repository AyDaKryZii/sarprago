<?php

namespace App\Filament\Admin\Resources\LoanItems\Pages;

use App\Filament\Admin\Resources\LoanItems\LoanItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanItem extends CreateRecord
{
    protected static string $resource = LoanItemResource::class;
}
