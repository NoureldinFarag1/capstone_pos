<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;

Route::middleware('api')->group(function () {
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::get('items/filter', [ItemController::class, 'filter']);
    Route::get('/api/items/search', [ItemController::class, 'searchByBarcode']);
    Route::get('/api/items/filter', [ItemController::class, 'filterItems']);
    // Add other routes as needed
});
