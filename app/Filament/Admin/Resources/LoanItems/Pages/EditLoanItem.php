<?php

namespace App\Filament\Admin\Resources\LoanItems\Pages;

use App\Filament\Admin\Resources\LoanItems\LoanItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditLoanItem extends EditRecord
{
    protected static string $resource = LoanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return $this->record->item->name;
    }
}
