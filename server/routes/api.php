<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAuthController;


Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Go Parking API.',
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [UserController::class, 'store'])->name('auth.register');
Route::post('/auth/login', [UserAuthController::class, 'store'])->name('auth.login');
Route::post('auth/logout', [UserAuthController::class, 'destroy'])->name('auth.logout');
