<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::redirect('/login', '/app/login')->name('login');

