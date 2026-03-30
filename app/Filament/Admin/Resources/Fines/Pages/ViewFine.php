<?php

namespace App\Filament\Admin\Resources\Fines\Pages;

use App\Filament\Admin\Resources\Fines\FineResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFine extends ViewRecord
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
