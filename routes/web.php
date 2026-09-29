<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChargingSessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChargingController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\InvoiceController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Location; 

// Mengalihkan halaman utama (/) langsung ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

// Logout (Hanya untuk yang sudah login)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth');

// Rute untuk Tamu / Belum Login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Terproteksi Login (User & Admin)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $nearestLocation = Location::where('status', 'aktif')->first();
        return view('dashboard', compact('nearestLocation'));
    })->name('dashboard');
    
    // Rute Top Up Saldo E-Wallet
    Route::get('/topup', [WalletController::class, 'index'])->name('topup.index');
    Route::post('/topup', [WalletController::class, 'store'])->name('topup.store');
    
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

    // Fitur Sistem Notifikasi (FR-08)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Fitur Scan & Charge
    Route::get('/scan-charge', [ChargingController::class, 'scan'])->name('scan.charge');
    Route::post('/scan-charge/process', [ChargingController::class, 'processScan'])->name('scan.charge.process');
    Route::get('/scan-charge/{charger}', [ChargingController::class, 'show'])->name('scan.charge.show');
    Route::post('/charging/session/{id}/resume', [ChargingSessionController::class, 'resume'])->name('charging.resume');

    // Tombol Start & Stop Charge (Diperbarui dengan parameter opsional {charger?})
    Route::post('/charging/start/{charger?}', [ChargingController::class, 'start'])->name('charging.start');
    
    Route::get('/charging/session/{session}', [ChargingSessionController::class, 'show'])->name('charging.session');
    Route::post('/charging/session/{session}/stop', [ChargingSessionController::class, 'stop'])->name('charging.stop');

    // FR-07 - Riwayat Transaksi & Sesi Pengguna
    Route::get('/riwayat', [ChargingSessionController::class, 'history'])->name('charging.history');

    // Rute untuk menampilkan halaman khusus Review Pembayaran
    Route::get('/charging/session/{session}/payment', [ChargingSessionController::class, 'paymentView'])->name('charging.payment.view');

    // Rute aksi proses bayar
    Route::post('/charging/pay/{session}', [ChargingSessionController::class, 'pay'])->name('charging.pay');

    // Rute Invoice & Struk Digital
    Route::get('/charging/{session}/invoice', [InvoiceController::class, 'show'])->name('charging.invoice');

    // Rute Download Invoice PDF
    Route::get('/charging/{session}/invoice/pdf', [InvoiceController::class, 'downloadPdf'])->name('charging.invoice.pdf');

    // Khusus Admin (Diproteksi AdminMiddleware - Milik Anda)
    Route::middleware([AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
        
        // Management Location/Stasiun
        Route::get('/stations', [StationController::class, 'index'])->name('stations.index');
        Route::post('/stations', [StationController::class, 'store'])->name('stations.store');
        Route::get('/stations/{id_location}/edit', [StationController::class, 'edit'])->name('stations.edit'); 
        Route::put('/stations/{id_location}', [StationController::class, 'update'])->name('stations.update');
        Route::delete('/stations/{id_location}', [StationController::class, 'destroy'])->name('stations.destroy');

        // Management Charger
        Route::post('/stations/{id_location}/chargers', [StationController::class, 'storeCharger'])->name('chargers.store');
        Route::delete('/chargers/{id_charger}', [StationController::class, 'destroyCharger'])->name('chargers.destroy');
    });

});