<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupDatabaseExport;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\CutisController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\RekrutmenController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Auth::routes(['register' => false]);

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', isAdmin::class]], function () {
    // Dashboard
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Jabatan Management
    Route::resource('jabatan', JabatanController::class);

    // Pegawai Management
    Route::resource('pegawai', PegawaiController::class);
    // Route::get('pegawai/akun', [PegawaiController::class, 'indexAdmin'])->name('pegawai.admin');

    // Impersonate
    Route::get('impersonate/{id}', [ImpersonateController::class, 'startImpersonation'])->name('impersonate');
    Route::get('/stop-impersonation', [ImpersonateController::class, 'stopImpersonation'])->name('impersonate.stop');

    // Penggajian Management
    Route::resource('penggajian', PenggajianController::class);

    // Rekrutmen Management
    Route::resource('rekrutmen', RekrutmenController::class);

    // Cuti Management
    Route::get('cuti/menu', [CutisController::class, 'menu'])->name('cuti.menu');
    Route::get('cuti/notifications', [CutisController::class, 'getNotifications'])->name('cuti.notifications');
    Route::put('cuti/approve/{id}', [CutisController::class, 'approve'])->name('cuti.approve');
    Route::put('cuti/reject/{id}', [CutisController::class, 'reject'])->name('cuti.reject');
    Route::get('izin-sakit', [WelcomeController::class, 'izinSakit'])->name('izin.sakit');

    // Berkas Management
    Route::resource('berkas', BerkasController::class);

    // Laporan Management
    Route::get('laporan/pegawai', [LaporanController::class, 'pegawai'])->name('laporan.pegawai');
    Route::get('laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('laporan/cuti', [LaporanController::class, 'cuti'])->name('laporan.cuti');

    // Database Backup
    Route::get('export-database', [BackupDatabaseExport::class, 'export'])->name('export-database');
});

// Socialite (Google Login) Routes
Route::get('/redirect', [SocialiteController::class, 'redirect'])->name('redirect')->middleware('guest');
Route::get('/callback', [SocialiteController::class, 'callback'])->name('callback')->middleware('guest');
Route::get('/logout', [SocialiteController::class, 'logout'])->name('socialite.logout')->middleware('auth');

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth']], function () {
    // Dashboard
    Route::get('dashboard', function () {
        return view('user.dashboard.index');
    })->name('user.dashboard');

    // Absensi Management
    Route::get('absensi', [WelcomeController::class, 'index'])->middleware('auth');
    Route::resource('absensi', WelcomeController::class)->names('welcome');
    Route::post('absen-sakit', [WelcomeController::class, 'absenSakit'])->name('welcome.absenSakit');
    Route::post('absen-pulang', [WelcomeController::class, 'absenPulang'])->name('welcome.absenPulang');

    // Penggajian Management
    Route::get('penggajian', [PenggajianController::class, 'index1'])->name('penggajian.index1');
    Route::delete('penggajian/{id}', [PenggajianController::class, 'destroy1'])->name('penggajian.destroy1');

    // Profile
    Route::get('profile', function () {
        return view('user.profile.index');
    });

    // Cuti Management
    Route::get('cuti', [CutisController::class, 'index'])->name('cuti.index');
    Route::post('cuti/store', [CutisController::class, 'store'])->name('cuti.store');
    Route::patch('cuti/update-status/{id}', [CutisController::class, 'updateStatus'])->name('cuti.updateStatus');
});
