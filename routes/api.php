<?php

use App\Http\Controllers\FlowerTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.docs');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::apiResource('flower-types', FlowerTypeController::class);