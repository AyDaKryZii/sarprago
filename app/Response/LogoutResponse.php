<?php

namespace App\Response;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Filament\Auth\Http\Responses\LogoutResponse as BaseLogoutResponse;

class LogoutResponse extends BaseLogoutResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function toResponse($request): RedirectResponse
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return redirect()->to(Filament::getLoginUrl());
        }
 
        return parent::toResponse($request);
    }
}
