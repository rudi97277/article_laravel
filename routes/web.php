<?php

use App\Http\Controllers\Web\CardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/card/{key}', [CardController::class, 'download']);
// Route::get('', fn () => view('emails.card'));
