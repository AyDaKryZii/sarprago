<?php

namespace App\Filament\Admin\Resources\LoanItems\Pages;

use App\Filament\Admin\Resources\LoanItems\LoanItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewLoanItem extends ViewRecord
{
    protected static string $resource = LoanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return $this->record->item->name;
    }
}
