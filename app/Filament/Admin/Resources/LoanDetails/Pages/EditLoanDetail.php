<?php

namespace App\Filament\Admin\Resources\LoanDetails\Pages;

use App\Filament\Admin\Resources\LoanDetails\LoanDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLoanDetail extends EditRecord
{
    protected static string $resource = LoanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
