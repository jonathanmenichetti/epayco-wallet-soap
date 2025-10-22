<?php

use App\Http\Controllers\ClientSoapController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletSoapController;

// SOAP routes
Route::prefix('soap')->group(function () {
    Route::get('clients', [ClientSoapController::class, 'wsdl']);
    Route::post('clients', [ClientSoapController::class, 'server']);
    Route::get('wallet', [WalletSoapController::class, 'wsdl']);
    Route::post('wallet', [WalletSoapController::class, 'server']);
});
