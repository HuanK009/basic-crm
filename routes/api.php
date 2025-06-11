<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [JWTAuthController::class, 'login']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::post('refresh', [JWTAuthController::class, 'refresh']);
    Route::post('register', [JWTAuthController::class, 'register']);
    Route::post('me', [JWTAuthController::class, 'me']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    // Route::get('profile/{id}', [UserController::class, 'getProfile']);
    Route::post('update', [UserController::class, 'update']);
    Route::post('avatar', [UserController::class, 'upload_avatar']);
    // Route::delete('delete/{id}', [UserController::class, 'delete']);
    // Route::get('list', [UserController::class, 'list']);
});
