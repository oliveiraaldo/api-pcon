<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

Route::get('/auth/cas-redirect', [AuthController::class, 'redirectToCas']);
Route::get('/auth/cas-callback', [AuthController::class, 'handleCasCallback']);
Route::middleware(['jwt.auth'])->get('/me', function () {
    return response()->json(Auth::user());
});
