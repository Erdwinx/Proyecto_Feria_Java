<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [TicketApiController::class, 'health']);

Route::prefix('customers')->group(function () {
    Route::post('/register', [CustomerApiController::class, 'register']);
    Route::post('/login', [CustomerApiController::class, 'login']);

    Route::middleware('jwt.customer')->group(function () {
        Route::get('/me', [CustomerApiController::class, 'me']);
        Route::get('/tickets', [CustomerApiController::class, 'listTickets']);
        Route::post('/tickets', [CustomerApiController::class, 'purchase']);
    });
});

Route::get('/tickets', [TicketApiController::class, 'listTickets']);
Route::post('/tickets', [TicketApiController::class, 'createTicket']);
Route::get('/tickets/{id}', [TicketApiController::class, 'getTicket']);
Route::get('/tickets/{id}/current-qr', [TicketApiController::class, 'getCurrentQr']);
Route::get('/scans', [TicketApiController::class, 'listScans']);
Route::post('/scan/validate', [TicketApiController::class, 'validateScan']);
Route::post('/scan/recover', [TicketApiController::class, 'recoverTicket']);
