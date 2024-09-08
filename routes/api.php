<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\LoginController;

Route::group(['prefix' => 'v1'], function() {
    Route::post('login', [LoginController::class, 'login']);
});

// Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\v1'], function() {
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\v1', 'middleware' => 'auth:sanctum'], function() {
    Route::apiResource('transaction', TransactionController::class);
    Route::apiResource('transaction-type', TransactionTypeController::class);
    Route::apiResource('user-wallet', UserWalletController::class);
});
