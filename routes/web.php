<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/verify', [VerificationController::class, 'verify']);

    Route::apiResource('tags', TagController::class);

    Route::apiResource('posts', PostController::class);
    Route::get('posts/deleted', [PostController::class, 'deleted']);
    Route::post('posts/{post}/restore', [PostController::class, 'restore']);
});
