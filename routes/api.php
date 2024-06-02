<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::get('account/{id}', [AccountController::class, 'show']);

Route::post('transfer', [AccountController::class, 'transaction']);
Route::get('extract/{id}', [AccountController::class, 'extractAccount']);
