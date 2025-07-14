<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// --- Public Routes (Authentication) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Authenticated Routes (Requires auth:sanctum middleware) ---
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes (Remain as explicit routes - not suitable for apiResource)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
Route::post('/user/profile-picture', [UserController::class, 'uploadProfilePhoto']);
    // Event Routes (Using apiResource)
    // This generates: GET, POST, GET/{id}, PUT/PATCH/{id}, DELETE/{id}
  // Protected Event Routes
    Route::apiResource('events', EventController::class)->except(['index', 'show']);
Route::post('/events/{event}/upload', [EventController::class, 'update']);
    // Booking Routes (Using apiResource, excluding 'create', 'edit', 'update' as per your simplified model)
    // This generates: GET, POST, GET/{id}, DELETE/{id}
    Route::apiResource('bookings', BookingController::class)->except(['create', 'edit', 'update']);
    // Alternatively, you can use ->only(['index', 'store', 'show', 'destroy']) which results in the same routes.
});
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);