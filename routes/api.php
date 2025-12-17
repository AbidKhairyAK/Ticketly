<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketApiController;

Route::get('/tickets/{ticket}', [TicketApiController::class, 'show'])->name('ticket-api.show');
Route::get('/tickets/by-email/{email}', [TicketApiController::class, 'index'])->name('ticket-api.index');
Route::get('/tickets/search/{email}/{query}', [TicketApiController::class, 'search'])->name('ticket-api.show');
Route::post('/tickets/create', [TicketApiController::class, 'store'])->name('ticket-api.store');
Route::patch('/tickets/update', [TicketApiController::class, 'update'])->name('ticket-api.update');
Route::patch('/tickets/close', [TicketApiController::class, 'close'])->name('ticket-api.close');