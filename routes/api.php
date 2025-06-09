<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FlowerController;
use App\Http\Controllers\FlowerTypeController;
use App\Http\Controllers\ImportReceiptController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.docs');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::apiResource('flower-types', FlowerTypeController::class);
Route::apiResource('flower', FlowerController::class);
Route::apiResource('import-receipts', ImportReceiptController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);