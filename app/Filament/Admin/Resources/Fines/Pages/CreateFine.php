<?php

namespace App\Filament\Admin\Resources\Fines\Pages;

use App\Filament\Admin\Resources\Fines\FineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFine extends CreateRecord
{
    protected static string $resource = FineResource::class;
}
