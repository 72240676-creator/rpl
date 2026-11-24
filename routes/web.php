
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChargingSessionController;
use App\Models\ChargingSession;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChargingController;
use App\Http\Controllers\LaporanPenggunaController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\PointController;
use App\Models\Location;


// ============================================================
// HALAMAN UTAMA
// ============================================================

Route::get('/', function () {
    return redirect('/login');
});


// ============================================================
// LOGOUT
// ============================================================

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth');


// ============================================================
// GUEST
// ============================================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

});


// ============================================================
// USER YANG SUDAH LOGIN
// ============================================================

Route::middleware('auth')->group(function () {


    // ========================================================
    // DASHBOARD USER
    // ========================================================

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ========================================================
    // E-WALLET / TOP UP
    // ========================================================

    Route::get('/topup', [WalletController::class, 'index'])
        ->name('topup.index');

    Route::post('/topup', [WalletController::class, 'store'])
        ->name('topup.store');


    // ========================================================
    // E-POIN / REWARD
    // ========================================================

    Route::get('/points', [PointController::class, 'index'])
        ->name('points.index');

    Route::get('/tukar-poin', [PointController::class, 'index'])
        ->name('poin.index');

    Route::post('/points/redeem', [PointController::class, 'redeem'])
        ->name('points.redeem');

    Route::post('/tukar-poin', [PointController::class, 'redeem'])
        ->name('poin.redeem');


    // ========================================================
    // PROFILE & KENDARAAN
    // ========================================================

    Route::get('/profile', [ProfileController::class, 'index']);

    Route::post('/profile/vehicle', [ProfileController::class, 'storeVehicle']);
    Route::put('/profile/vehicle/{vehicle}', [ProfileController::class, 'updateVehicle'])->name('vehicles.update');
    Route::post('/profile/vehicle/select', [ProfileController::class, 'selectVehicle'])->name('vehicles.select');
    Route::delete('/profile/vehicle/{vehicle}', [ProfileController::class, 'destroyVehicle'])->name('vehicles.destroy');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/vehicles/{vehicle}/discharge', [ProfileController::class, 'discharge'])->name('vehicles.discharge');


    // ========================================================
    // LOKASI SPKLU USER
    // ========================================================

    Route::get('/locations', [LocationController::class, 'index'])
        ->name('locations.index');


    // ========================================================
    // OPERATOR
    // ========================================================

    Route::middleware('operator')
        ->prefix('operator')
        ->name('operator.')
        ->group(function () {

            // Dashboard Operator
            Route::get('/dashboard', [OperatorDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/dashboard/live', [OperatorDashboardController::class, 'live'])
                ->name('dashboard.live');


            // Aktivitas Charging
            Route::get('/activity', [OperatorDashboardController::class, 'activity'])
                ->name('activity');


            // Pemeriksaan Charger
            Route::put('/chargers/{id_charger}/inspection',
                [OperatorDashboardController::class, 'updateInspection'])
                ->name('chargers.inspection.update');


            // =================================================
            // STASIUN
            // =================================================

            Route::get('/stations', [StationController::class, 'index'])
                ->name('stations.index');

            Route::post('/stations', [StationController::class, 'store'])
                ->name('stations.store');

            Route::get('/stations/{id_location}/edit', [StationController::class, 'edit'])
                ->name('stations.edit');

            Route::put('/stations/{id_location}', [StationController::class, 'update'])
                ->name('stations.update');

            Route::delete('/stations/{id_location}', [StationController::class, 'destroy'])
                ->name('stations.destroy');


            // Charger pada Stasiun
            Route::post('/stations/{id_location}/chargers', [StationController::class, 'storeCharger'])
                ->name('chargers.store');

            Route::delete('/chargers/{id_charger}', [StationController::class, 'destroyCharger'])
                ->name('chargers.destroy');


            // Tarif Charger
            Route::put('/chargers/{id_charger}/tariff', [StationController::class, 'updateChargerTariff'])
                ->name('chargers.tariff.update');

            Route::get('/chargers/{id_charger}/tariff', [OperatorDashboardController::class, 'editTariff'])
                ->name('chargers.tariff.edit');


            // =================================================
            // LOKASI OPERATOR
            // =================================================

            Route::get('/locations', [LocationController::class, 'operatorIndex'])
                ->name('locations');

            Route::post('/locations', [LocationController::class, 'store'])
                ->name('locations.store');

            Route::put('/locations/{id}', [LocationController::class, 'update'])
                ->name('locations.update');


            // =================================================
            // LAPORAN PEMERIKSAAN
            // =================================================

            Route::get('/reports/inspections', [OperatorDashboardController::class, 'inspectionReports'])
                ->name('reports.inspections');


            // =================================================
            // LAPORAN PENGGUNA
            // =================================================

            Route::get('/reports/users',[OperatorDashboardController::class, 'userReports']
            )->name('reports.users');

            Route::post('/reports/users/{id_laporan}/forward',
                [OperatorDashboardController::class, 'forwardUserReportToAdmin']
            )->name('reports.users.forward');

            Route::put('/reports/users/{id_laporan}/status',
                [OperatorDashboardController::class, 'updateUserReportStatus']
            )->name('reports.users.status.update');

        });


    // ========================================================
    // NOTIFICATION
    // ========================================================

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');


    // ========================================================
    // SCAN CHARGER
    // ========================================================

    Route::get('/scan-charge', [ChargingController::class, 'scan'])->name('scan.charge');
    Route::post('/scan-charge/lookup', [ChargingController::class, 'lookup'])->name('scan.charge.lookup');
    Route::post('/scan-charge/process', [ChargingController::class, 'processScan'])->name('scan.charge.process');
    Route::get('/scan-charge/{charger}', [ChargingController::class, 'show'])->name('scan.charge.show');


    // ========================================================
    // CHARGING SESSION
    // ========================================================

    Route::post('/charging/start', [ChargingSessionController::class, 'start'])
        ->name('charging.start');

    Route::post('/charging/session/{id}/resume', [ChargingSessionController::class, 'resume'])
        ->name('charging.resume');

    Route::get('/charging/session/{session}', [ChargingSessionController::class, 'show'])
        ->name('charging.session');

    Route::post('/charging/session/{session}/stop', [ChargingSessionController::class, 'stop'])
        ->name('charging.stop');


    // ========================================================
    // PAYMENT
    // ========================================================

    Route::get('/charging/session/{session}/payment', [ChargingSessionController::class, 'paymentView'])
        ->name('charging.payment.view');

    Route::post('/charging/pay/{session}', [ChargingSessionController::class, 'pay'])
        ->name('charging.pay');


    // ========================================================
    // RIWAYAT CHARGING
    // ========================================================

    Route::get('/riwayat', [ChargingSessionController::class, 'history'])
        ->name('charging.history');


    // ========================================================
    // INVOICE
    // ========================================================

    Route::get('/charging/{session}/invoice', [InvoiceController::class, 'show'])
        ->name('charging.invoice');

    Route::get('/charging/{session}/invoice/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('charging.invoice.pdf');


    // ========================================================
    // ADMIN
    // ========================================================

    Route::middleware([AdminMiddleware::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard Admin
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::put('/chargers/{id_charger}/inspection',
                [AdminDashboardController::class, 'updateInspection'])
                ->name('chargers.inspection.update');


            // Laporan Transaksi & Pendapatan
            Route::get('/reports', [ReportController::class, 'index'])
                ->name('reports.index');

        });


    // ========================================================
    // FORM LAPORAN PENGGUNA
    // ========================================================

    Route::post('/laporan-pengguna', [LaporanPenggunaController::class, 'store'])
        ->name('laporan-pengguna.store');

});
