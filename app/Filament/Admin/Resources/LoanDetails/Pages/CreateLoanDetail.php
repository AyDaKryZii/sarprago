<?php

namespace App\Filament\Admin\Resources\LoanDetails\Pages;

use App\Filament\Admin\Resources\LoanDetails\LoanDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanDetail extends CreateRecord
{
    protected static string $resource = LoanDetailResource::class;
}
