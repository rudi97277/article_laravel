<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MembershipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/user', [AuthController::class, 'profile']);

    Route::prefix('articles')->group(function () {
        Route::controller(ArticleController::class)->group(function () {
            Route::post('', 'store');
            Route::put('{id}', 'update');
            Route::patch('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
    });

    Route::prefix('events')->group(function () {
        Route::controller(EventController::class)->group(function () {
            Route::post('', 'store');
            Route::put('{id}', 'update');
            Route::patch('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
    });

    Route::prefix('memberships')->group(function () {
        Route::controller(MembershipController::class)->group(function () {
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::patch('{id}', 'update');
        });
    });
});

Route::prefix('articles')->group(function () {
    Route::controller(ArticleController::class)->group(function () {
        Route::get('', 'index');
        Route::get('{id}', 'show');
    });
});

Route::prefix('events')->group(function () {
    Route::controller(EventController::class)->group(function () {
        Route::get('', 'index');
        Route::get('{id}', 'show');
    });
});

Route::prefix('memberships')->group(function () {
    Route::controller(MembershipController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('{id}', 'show');
        Route::patch('{id}/evidence', 'updateEvidence');
    });
});

Route::post('documents/upload', [DocumentController::class, 'upload']);
