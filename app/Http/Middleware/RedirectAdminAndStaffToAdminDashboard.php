<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Pages\Dashboard;

class RedirectAdminAndStaffToAdminDashboard
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        if ($request->is('admin/*')) {
            if (auth()->user()->canAccessAdminPanel()) {
                return redirect()->to(Dashboard::getUrl(panel: 'admin'));
            } else {
                return redirect()->to(Dashboard::getUrl(panel: 'app'));
            }
        }
        return $next($request);
    }
}