<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FlowerController;
use App\Http\Controllers\FlowerTypeController;
use App\Http\Controllers\ImportReceiptController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.docs');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('user/update', [UserController::class, 'updateProfile']);

    Route::get('orders/user/{id}', [OrderController::class, 'OrderDetailById']);

    Route::get('orders/details', [OrderController::class, 'OrderDetailUser']);

});

Route::apiResource('flower-types', FlowerTypeController::class);
Route::apiResource('flower', FlowerController::class);
Route::apiResource('import-receipts', ImportReceiptController::class);
//Route::apiResource('categories', CategoryController::class);
//dung prefix đi
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});



//Route::apiResource('products', ProductController::class);

Route::prefix('products')->group(function () {
    Route::get('/stock', [ProductController::class, 'checkAllStock']);
    Route::get('/{id}/stock', [ProductController::class, 'checkStock']);
    Route::get('/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);

    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::put('/{id}/hide', [ProductController::class, 'hide']);
});

Route::apiResource('orders',OrderController::class);
Route::apiResource('discounts',DiscountController::class);


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

