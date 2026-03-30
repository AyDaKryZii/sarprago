<?php

namespace App\Filament\Admin\Resources\Loans\Pages;

use App\Filament\Admin\Resources\Loans\LoanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
