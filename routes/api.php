<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('v1')->group(function () {
    Route::post('/transfers',[TransferController::class, 'store']);
    Route::get('/accounts/{id}/balance',[AccountController::class, 'balance']);
    Route::post('/transfers/{id}/reverse',[TransferController::class, 'reverse']);
    Route::get('/transfers/{id}',[TransferController::class, 'show']);
});