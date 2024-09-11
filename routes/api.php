<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JWTAuthenticate;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;

Route::group(['prefix' => 'v1', 'middleware' => ['api']], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/my-profile', [AuthController::class, 'myProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('category')->middleware(['api'])->group(function () {
        // Public routes
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{slug}/products', [CategoryController::class, 'getProducts']);

        // Admin routes
        Route::middleware([JWTAuthenticate::class, 'isAdmin'])->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{category}', [CategoryController::class, 'update']);
            Route::delete('/{category}', [CategoryController::class, 'destroy']);
        });
    });

    Route::prefix('product')->middleware(['api'])->group(function () {
        // Public routes
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{slug}', [ProductController::class, 'getProduct']);

        // Admin and Seller routes
        Route::middleware([JWTAuthenticate::class, 'seller_or_admin'])->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{slug}', [ProductController::class, 'update']);
            Route::delete('/{slug}', [ProductController::class, 'destroy']);
        });

        // Review routes
        Route::middleware([JWTAuthenticate::class, 'JWTAuthenticate'])->group(function () {
            Route::post('{productId}/reviews', [ProductController::class, 'storeReview']);
        });
    });

    Route::prefix('orders')->middleware(['api'])->group(function () {
        // Public routes
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);

        // All user routes
        Route::middleware([JWTAuthenticate::class, 'JWTAuthenticate'])->group(function () {
            Route::post('/', [OrderController::class, 'store']);
            Route::put('/{id}', [OrderController::class, 'update']);
            Route::delete('/{id}', [OrderController::class, 'destroy']);
        });
    });
});
