<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JWTAuthenticate;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;

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
        Route::get('/{slug}/products', [CategoryController::class, 'products']);

        // Admin routes
        Route::middleware([JWTAuthenticate::class, 'isAdmin'])->group(function () {
            Route::post('/', [CategoryController::class, 'store']); // For creating a category
            Route::put('/{category}', [CategoryController::class, 'update']); // For updating a category
            Route::delete('/{category}', [CategoryController::class, 'destroy']); // For deleting a category
        });
    });
});
