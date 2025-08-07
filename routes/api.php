<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;

Route::prefix('v1')->group(function () {

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-reset-code', [AuthController::class, 'verifyResetCode']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});
Route::prefix('category')->group(function () {
    Route::get('/list', [CategoryController::class, 'index']);
});
Route::prefix('product')->group(function () {
    Route::get('/list', [ProductController::class, 'ProductList']);
    Route::get('/special-offers', [ProductController::class, 'getSpecialOfferProducts']);
    Route::get('/promoted', [ProductController::class, 'getPromotedProducts']);
    Route::get('/category/{category_id}', [ProductController::class, 'getProductsByCategory']);
});


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/index', [UserController::class, 'index']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });


    Route::prefix('cart')->group(function () {
        Route::get('/index', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::delete('/remove', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::post('/checkout', [CartController::class, 'checkout']);
        Route::post('/update-quantity', [CartController::class, 'updateQuantity']);

    });
    Route::prefix('wishlist')->group(function () {
        Route::get('/add/{product_id}', [WishListController::class, 'addToWishlist']);
        Route::get('/remove/{product_id}', [WishListController::class, 'removeFromWishlist']);
        Route::get('/list', [WishListController::class, 'getWhishlistProductsByUser']);
    });
    Route::prefix('order')->group(function () {
        Route::post('/checkout', [OrderController::class, 'createOrder']);
        Route::get('/getOrders', [OrderController::class, 'getUserOrders']);
    });

});

});
