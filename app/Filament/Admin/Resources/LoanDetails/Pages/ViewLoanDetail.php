<?php

namespace App\Filament\Admin\Resources\LoanDetails\Pages;

use App\Filament\Admin\Resources\LoanDetails\LoanDetailResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanDetail extends ViewRecord
{
    protected static string $resource = LoanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
