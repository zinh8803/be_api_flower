<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\AutoImportReceiptController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FlowerController;
use App\Http\Controllers\FlowerTypeController;
use App\Http\Controllers\ImportReceiptController;
use App\Http\Controllers\MoMoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VnPayController;
use App\Http\Middleware\CheckJWT;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.docs');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('auth:api')->group(function () {
Route::middleware(CheckJWT::class)->group(function () {

    Route::get('profile', [UserController::class, 'profile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::Put('user/update', [UserController::class, 'updateProfile']);

    Route::get('orders/user/{id}', [OrderController::class, 'OrderDetailById']);

    Route::get('orders/details', [OrderController::class, 'OrderDetailUser']);
    Route::apiResource('orders',OrderController::class);
    
    Route::get('/admin/dashboard/statistics', [DashBoardController::class, 'statistics']);

    Route::resource('auto-import-receipts', AutoImportReceiptController::class);

});
Route::get('/admin/orders/details/{id}', [OrderController::class, 'show']);
Route::post('/refresh-token', [UserController::class, 'refreshToken']);
Route::apiResource('flower-types', FlowerTypeController::class);
Route::apiResource('flower', FlowerController::class);
Route::apiResource('import-receipts', ImportReceiptController::class);
//Route::apiResource('categories', CategoryController::class);
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});



//Route::apiResource('products', ProductController::class);

Route::prefix('products')->group(function () {
    Route::get('/stock-warning/search', [ProductController::class, 'searchStockWarning']);
    Route::get('/stock-warning', [ProductController::class, 'stockWarning']);
    Route::get('/check-available-products', [ProductController::class, 'checkAvailableProducts']);
    Route::get('/stock', [ProductController::class, 'checkAllStock']);
    Route::get('/{id}/stock', [ProductController::class, 'checkStock']);
    Route::get('/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);

    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::put('/{id}/hide', [ProductController::class, 'hide']);

});

Route::apiResource('discounts',DiscountController::class);
Route::post('discounts/check-code', [DiscountController::class, 'checkCode']);

Route::get('users/getall', [UserController::class, 'index']);
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::post('/send-otp', [UserController::class, 'sendOtp']);

Route::post('/payment',[VnPayController::class, 'createPayment']); 
Route::get('/vnpay_return', [VnPayController::class, 'vnpayReturn']); 
