<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Models\Location; 

// Mengalihkan halaman utama (/) langsung ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth');

// Rute Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);

// Rute Pendaftaran
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $nearestLocation = Location::where('status', 'aktif')->first();
        return view('dashboard', compact('nearestLocation'));
    })->name('dashboard');

    // Rute Profile & Kendaraan
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/vehicle', [ProfileController::class, 'storeVehicle']);
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // Fitur Pengemudi: Cari & Lihat Lokasi SPKLU
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    // Fitur Operator: Panel CRUD Lokasi SPKLU
    Route::get('/operator/locations', [LocationController::class, 'operatorIndex'])->name('operator.locations');
    Route::post('/operator/locations', [LocationController::class, 'store'])->name('operator.locations.store');
    Route::put('/operator/locations/{id}', [LocationController::class, 'update'])->name('operator.locations.update');

    // Fitur No. 6: Sistem Notifikasi (FR-08)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

});