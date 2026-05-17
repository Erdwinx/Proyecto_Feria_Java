<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/health', [TicketApiController::class, 'health']);

    Route::get('/events', function () {
        $events = App\Models\Event::query()
            ->orderBy('fecha_evento')
            ->orderBy('nombre')
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'event_key' => Str::slug((string) $event->nombre),
                    'nombre' => $event->nombre,
                    'tipo_evento' => $event->tipo_evento,
                    'fecha_evento' => optional($event->fecha_evento)?->format('Y-m-d'),
                ];
            })
            ->values();

        return response()->json($events);
    });
    Route::prefix('customers')->group(function () {
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/register', [CustomerApiController::class, 'register']);
            Route::post('/login', [CustomerApiController::class, 'login']);
        });

        Route::middleware(['jwt.customer', 'throttle:30,1'])->group(function () {
            Route::get('/me', [CustomerApiController::class, 'me']);
            Route::get('/tickets', [CustomerApiController::class, 'listTickets']);
            Route::post('/tickets', [CustomerApiController::class, 'purchase']);
            Route::post('/tickets/queue', [CustomerApiController::class, 'queuePurchase']);
            Route::get('/tickets/queue/{id}', [CustomerApiController::class, 'queuePurchaseStatus']);
        });
    });

    Route::get('/tickets', [TicketApiController::class, 'listTickets']);
    Route::get('/tickets/available-seats', [TicketApiController::class, 'availableSeats']);
    Route::post('/tickets', [TicketApiController::class, 'createTicket']);
    Route::get('/tickets/{id}', [TicketApiController::class, 'getTicket']);
    Route::get('/tickets/{id}/current-qr', [TicketApiController::class, 'getCurrentQr']);
    Route::get('/scans', [TicketApiController::class, 'listScans']);
    Route::post('/scan/validate', [TicketApiController::class, 'validateScan']);
    Route::post('/scan/recover', [TicketApiController::class, 'recoverTicket']);
});
