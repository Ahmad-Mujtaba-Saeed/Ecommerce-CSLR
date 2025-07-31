<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;







Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    // Password reset routes
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-reset-code', [AuthController::class, 'verifyResetCode']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});
Route::prefix('product')->group(function () {
    Route::get('/list', [ProductController::class, 'ProductList']);
    Route::get('/special-offers', [ProductController::class, 'getSpecialOfferProducts']);
    Route::get('/promoted', [ProductController::class, 'getPromotedProducts']);
    Route::get('/category/{category_id}', [ProductController::class, 'getProductsByCategory']);
});



Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('/index', [UserController::class, 'index']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });
});
