<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/asets', [AsetController::class, 'index']);
