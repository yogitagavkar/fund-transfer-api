<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AccountController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
        Route::post('/transfers',[TransferController::class, 'store'])->middleware('throttle:write-api');
        Route::post('/transfers/{id}/reverse',[TransferController::class, 'reverse'])->middleware('throttle:write-api');
        Route::get('/accounts/{id}/balance',[AccountController::class, 'balance'])->middleware('throttle:read-api');
        Route::get('/transfers/{id}',[TransferController::class, 'show'])->middleware('throttle:read-api');
    });