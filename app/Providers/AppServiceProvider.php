<?php

namespace App\Providers;

use App\Response\LoginResponse;
use App\Response\LogoutResponse;
use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;
use Filament\Auth\Http\Responses\LogoutResponse as BaseLogoutResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        BaseLoginResponse::class => LoginResponse::class,
        BaseLogoutResponse::class => LogoutResponse::class,
    ];
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
