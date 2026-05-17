<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', fn () => redirect('/login'));
Route::get('/boletos', [AdminController::class, 'panel']);
Route::view('/inicio', 'inicio');
Route::view('/eventos', 'eventos');
Route::view('/noticias', 'noticias');
Route::view('/promociones', 'promociones');
Route::view('/mis-boletos', 'mis-boletos');
Route::view('/login', 'login');
Route::view('/comprar', 'comprar');
Route::get('/panel', fn () => redirect('/boletos'));
Route::get('/emisor', fn () => redirect('/boletos'));
Route::view('/scanner', 'scanner');
Route::get('/admin', [AdminController::class, 'dashboard']);
