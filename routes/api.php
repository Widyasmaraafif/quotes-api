<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/quotes', [QuoteController::class, 'index']);
Route::get('/quotes/random', [QuoteController::class, 'random']);
Route::post('/quotes', [QuoteController::class, 'store']);
Route::delete('/quotes/{id}', [QuoteController::class, 'destroy']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/quotes/category/{id}', [QuoteController::class, 'byCategory']);
Route::get('/quotes/category/name/{name}', [QuoteController::class, 'byCategoryName']);