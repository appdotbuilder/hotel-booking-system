<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Hotel homepage - main functionality
Route::controller(HotelController::class)->group(function () {
    Route::get('/', 'index')->name('hotel.index');
    Route::post('/', 'store')->name('hotel.search');
});

// Room management routes (Admin only)
Route::resource('rooms', RoomController::class)
    ->middleware(['auth']); 

// Booking routes
Route::resource('bookings', BookingController::class)
    ->middleware(['auth']);

// User management routes (Superadmin only)  
Route::resource('users', UserController::class)
    ->only(['index', 'show', 'edit', 'update', 'destroy'])
    ->middleware(['auth']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect('/');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
