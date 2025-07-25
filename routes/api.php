<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\AutoImportReceiptController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FlowerController;
use App\Http\Controllers\FlowerTypeController;
use App\Http\Controllers\ImportReceiptController;
use App\Http\Controllers\MoMoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VnPayController;
use App\Http\Middleware\CheckJWT;
use App\Http\Middleware\CheckRole;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.docs');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//flowers


Route::middleware(['check.role:admin'])->group(function () {
    //nhan vien
    Route::get('/admin/employees', [AdminController::class, 'index']);
    Route::post('admin/create-employee', [AdminController::class, 'createEmployee']);
    Route::put('admin/update-employee/{id}', [AdminController::class, 'updateEmployee']);

    //categories
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::post('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::post('/{slug}', [CategoryController::class, 'update']);
        Route::delete('/{slug}', [CategoryController::class, 'destroy']);
    });

    //products
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    //discounts
    Route::prefix('discounts')->group(function () {

        Route::post('/', [DiscountController::class, 'store']);
        Route::put('/{id}', [DiscountController::class, 'update']);
        Route::delete('/{id}', [DiscountController::class, 'destroy']);
    });

    //flower-types
    Route::prefix('flower-types')->group(function () {
        Route::post('/', [FlowerTypeController::class, 'store']);
        Route::put('/{id}', [FlowerTypeController::class, 'update']);
        Route::delete('/{id}', [FlowerTypeController::class, 'destroy']);
    });

    //flower
    Route::prefix('flower')->group(function () {
        Route::post('/', [FlowerController::class, 'store']);
        Route::put('/{id}', [FlowerController::class, 'update']);
        Route::delete('/{id}', [FlowerController::class, 'destroy']);
    });
});

Route::middleware(['check.role:user'])->group(function () {

    //orders
    Route::put('orders/cancel/{id}', [OrderController::class, 'cancelOrder']);
    Route::post('/orders/product-reports', [ProductReportController::class, 'store']);
    Route::delete('/orders/product-reports/{id}', [ProductReportController::class, 'delete']);
    Route::get('orders/details', [OrderController::class, 'OrderDetailUser']);

    //discounts
    Route::post('discounts/check-code', [DiscountController::class, 'checkCode']);
});

Route::middleware(['check.role:admin,employee,user'])->group(function () {
    // profile
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::Put('user/update', [UserController::class, 'updateProfile']);
    Route::put('change-password', [UserController::class, 'changePassword']);

    //report
    Route::post('/orders/product-reports', [ProductReportController::class, 'store']);

    //products
    Route::prefix('products')->group(function () {

        Route::put('/{id}/hide', [ProductController::class, 'hide']);
    });

    //discounts

});

Route::middleware(['check.role:admin,employee'])->group(function () {
    // dashboard
    Route::get('/admin/dashboard/statistics', [DashBoardController::class, 'statistics']);

    //orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
        Route::put('/product-reports', [ProductReportController::class, 'update']);
        Route::put('/returns/status', [OrderController::class, 'updateStatusOrderReturn']);
    });

    //notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);
    Route::delete('/notifications', [NotificationController::class, 'deleteAll']);

    //auto-import-receipts
    Route::resource('auto-import-receipts', AutoImportReceiptController::class);

    //import-receipts
    Route::apiResource('import-receipts', ImportReceiptController::class);

    //users
    Route::get('users/getall', [UserController::class, 'index']);
});
Route::middleware(['checkjwt'])->group(function () {
    Route::get('orders/user/{id}', [OrderController::class, 'OrderDetailById']);
});

// Route::post('orders', [OrderController::class, 'store']);
Route::get('/admin/orders/details/{id}', [OrderController::class, 'show']);
Route::post('/refresh-token', [UserController::class, 'refreshToken']);
// Route::apiResource('flower-types', FlowerTypeController::class);
// Route::apiResource('flower', FlowerController::class);
//Route::apiResource('import-receipts', ImportReceiptController::class);

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::post('/send-otp', [UserController::class, 'sendOtp']);
Route::post('/send-otp-reset-password', [UserController::class, 'sendOtpResetPassword']);
Route::put('/reset-password', [UserController::class, 'resetPassword']);
Route::post('/payment', [VnPayController::class, 'createPayment']);
Route::get('/vnpay_return', [VnPayController::class, 'vnpayReturn']);


//flower-types
Route::prefix('flower-types')->group(function () {
    Route::get('/', [FlowerTypeController::class, 'index']);
    Route::get('/{id}', [FlowerTypeController::class, 'show']);
});

Route::prefix('flower')->group(function () {
    Route::get('/', [FlowerController::class, 'index']);
    Route::get('/{id}', [FlowerController::class, 'show']);
});

//categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::get('/{slug}', [CategoryController::class, 'show']);
});

//products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/stock-warning/search', [ProductController::class, 'searchStockWarning']);
    Route::get('/stock-warning', [ProductController::class, 'stockWarning']);
    Route::get('/check-available-products', [ProductController::class, 'checkAvailableProducts']);
    Route::get('/stock', [ProductController::class, 'checkAllStock']);
    Route::get('/{id}/stock', [ProductController::class, 'checkStock']);
    Route::get('/category/id={categoryId}', [ProductController::class, 'getProductsByCategoryId']);
    Route::get('/category/{slug}', [ProductController::class, 'getProductsByCategory']);
    Route::get('/filter', [ProductController::class, 'filter']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/detailId/{id}', [ProductController::class, 'showbyId']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/check-available-products', [ProductController::class, 'checkAvailableProducts']);
});

//orders
Route::post('orders', [OrderController::class, 'store']);

//discounts
Route::prefix('discounts')->group(function () {
    Route::get('/', [DiscountController::class, 'index']);
    Route::get('/{id}', [DiscountController::class, 'show']);
});
