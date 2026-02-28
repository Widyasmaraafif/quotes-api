<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TagController;

Route::middleware('throttle:api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::get('/quotes/random', [QuoteController::class, 'random']);
    Route::get('/quotes/qotd', [QuoteController::class, 'qotd']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/quotes/category/{id}', [QuoteController::class, 'byCategory']);
    Route::get('/quotes/category/name/{name}', [QuoteController::class, 'byCategoryName']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/quotes/tag/{slug}', [QuoteController::class, 'byTag']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::post('/quotes', [QuoteController::class, 'store']);
        Route::delete('/quotes/{id}', [QuoteController::class, 'destroy']);
        Route::post('/quotes/{id}/like', [QuoteController::class, 'toggleLike']);
        
        Route::post('/categories', [CategoryController::class, 'store']);
    });
});