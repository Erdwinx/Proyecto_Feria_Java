<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));
Route::view('/boletos', 'boletos');
Route::view('/inicio', 'inicio');
Route::view('/eventos', 'eventos');
Route::view('/noticias', 'noticias');
Route::view('/promociones', 'promociones');
Route::view('/mis-boletos', 'mis-boletos');
Route::view('/login', 'login');
Route::view('/comprar', 'comprar');
Route::get('/emisor', fn () => redirect('/boletos'));
Route::view('/scanner', 'scanner');
