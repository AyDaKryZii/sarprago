<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::redirect('/login', '/app/login')->name('login');

Route::get('/report/summary', [ReportController::class, 'downloadAllLoansReport'])
    ->name('loan.report.summary');