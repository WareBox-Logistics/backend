<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/registerEmployee',[AuthController::class, 'registerEmployee']);
Route::post('/loginEmployee', [AuthController::class, 'loginEmployee']);
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');



