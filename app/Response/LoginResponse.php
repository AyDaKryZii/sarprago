<?php

namespace App\Response;

use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;

class LoginResponse extends BaseLoginResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function toResponse($request): Redirector|RedirectResponse
    {
        if (auth()->user()->canAccessAdminPanel()) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        return parent::toResponse($request);
    }
}
