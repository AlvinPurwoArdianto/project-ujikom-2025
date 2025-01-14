<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupDatabaseExport;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\CutisController;
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

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Auth::routes([
    'register' => false,
]);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', isAdmin::class]], function () {
    //route dashboard
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //route jabatan
    Route::resource('jabatan', JabatanController::class);

    //route pegawai
    Route::resource('pegawai', PegawaiController::class);
    Route::get('pegawai/akun', [PegawaiController::class, 'indexAdmin'])->name('pegawai.admin');

    //route penggajian
    Route::resource('penggajian', PenggajianController::class);

    //route rekrutmen
    Route::resource('rekrutmen', RekrutmenController::class);

    //Route cuti
    Route::get('cuti/menu', [CutisController::class, 'menu'])->name('cuti.menu');
    Route::get('cuti/notifications', [CutisController::class, 'getNotifications'])->name('cuti.notifications');
    Route::put('/cuti/approve/{id}', [CutisController::class, 'approve'])->name('cuti.approve');
    Route::put('/cuti/reject/{id}', [CutisController::class, 'reject'])->name('cuti.reject');
    Route::get('/izin-sakit', [WelcomeController::class, 'izinSakit'])->name('izin.sakit');

    //Route berkas
    Route::resource('berkas', BerkasController::class);

    //Route laporan
    Route::get('laporan/pegawai', [LaporanController::class, 'pegawai'])->name('laporan.pegawai');
    Route::get('laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('laporan/cuti', [LaporanController::class, 'cuti'])->name('laporan.cuti');

    //Route Buat Backup Database
    Route::get('/export-database', [BackupDatabaseExport::class, 'export'])->name('export-database');

});

// LOGIN GOOGLE
Route::get('/redirect', [SocialiteController::class, 'redirect'])->name('redirect')->middleware('guest');
Route::get('/callback', [SocialiteController::class, 'callback'])->name('callback')->middleware('guest');
Route::get('/logout', [SocialiteController::class, 'logout'])->name('socialite.logout')->middleware('auth');

Route::group(['prefix' => 'user', 'middleware' => ['auth']], function () {
    Route::get('dashboard', function () {
        return view('user.dashboard.index');
    });
    // Route::get('dashboard', [HomeController::class, 'index1'])->name('dashboard');

    // Route::get('absensi', [WelcomeController::class, 'index'])->name('welcome.index');
    Route::get('/absensi', [WelcomeController::class, 'index'])->middleware('auth');
    Route::resource('/absensi', WelcomeController::class)->names('welcome');
    Route::put('/{id}/update', [WelcomeController::class, 'update'])->name('welcome.update');
    Route::get('absensi/create', [WelcomeController::class, 'create'])->name('welcome.create');
    Route::post('absensi', [WelcomeController::class, 'store'])->name('welcome.store');
    Route::get('absensi/{id}/edit', [WelcomeController::class, 'edit'])->name('welcome.edit');
    Route::post('absensi/{id}', [WelcomeController::class, 'update'])->name('welcome.update');
    Route::post('/absen-sakit', [WelcomeController::class, 'absenSakit'])->name('welcome.absenSakit');
    Route::post('/absen-pulang', [WelcomeController::class, 'absenPulang'])->name('welcome.absenPulang');
    // Route::post('/absen-sakit', [WelcomeController::class, 'absenSakit'])->name('welcome.absenSakit');

    Route::get('penggajian', [PenggajianController::class, 'index1'])->name('penggajian.index1');
    Route::get('penggajian/create', [PenggajianController::class, 'create1'])->name('penggajian.create1');
    Route::post('penggajian', [PenggajianController::class, 'store1'])->name('penggajian.store1');
    Route::get('penggajian/{id}', [PenggajianController::class, 'show1'])->name('penggajian.show1');
    Route::get('penggajian/{id}/edit', [PenggajianController::class, 'edit1'])->name('penggajian.edit1');
    Route::put('penggajian/{id}', [PenggajianController::class, 'update1'])->name('penggajian.update1');
    Route::delete('penggajian/{id}', [PenggajianController::class, 'destroy1'])->name('penggajian.destroy1');

    Route::get('profile', function () {
        return view('user.profile.index');
    });

    Route::get('cuti', [CutisController::class, 'index'])->name('cuti.index');
    Route::post('/cuti/store', [CutisController::class, 'store'])->name('cuti.store');

    Route::patch('/cuti/update-status/{id}', [CutisController::class, 'updateStatus'])->name('cuti.updateStatus');

});
