<?php

namespace App\Filament\App\Resources\MyLoans\Pages;

use App\Filament\App\Resources\MyLoans\MyLoanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMyLoan extends EditRecord
{
    protected static string $resource = MyLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
