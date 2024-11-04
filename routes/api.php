<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RegionsController;
use App\Http\Middleware\ValidateCustomerData;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'create']);

Route::middleware(['validate_tokens'])->group(function () {
    Route::prefix('v1/')->group(function () {
        Route::resource('regions', RegionsController::class);
        Route::resource('communes', CommuneController::class);

        Route::middleware([ValidateCustomerData::class])->group(function () {
            Route::post('customers', [CustomerController::class, 'store']);
            Route::put('customers/{id}', [CustomerController::class, 'update']);
            Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
            Route::get('customers', [CustomerController::class, 'index']);
        });

        Route::get('logout', [AuthController::class, 'logout']);
    });
});
