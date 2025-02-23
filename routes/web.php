<?php

use App\Http\Controllers\AbsensiController;
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
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', isAdmin::class]], function () {
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('jabatan', JabatanController::class);
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('penggajian', PenggajianController::class);
    Route::resource('rekrutmen', RekrutmenController::class);
    Route::resource('berkas', BerkasController::class);

    Route::get('cuti/menu', [CutisController::class, 'menu'])->name('cuti.menu');
    Route::get('cuti/notifications', [CutisController::class, 'getNotifications'])->name('cuti.notifications');
    Route::put('cuti/approve/{id}', [CutisController::class, 'approve'])->name('cuti.approve');
    Route::put('cuti/reject/{id}', [CutisController::class, 'reject'])->name('cuti.reject');
    Route::get('izin-sakit', [AbsensiController::class, 'izinSakit'])->name('izin.sakit');

    Route::get('laporan/pegawai', [LaporanController::class, 'pegawai'])->name('laporan.pegawai');
    Route::get('laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('laporan/cuti', [LaporanController::class, 'cuti'])->name('laporan.cuti');
    Route::get('laporan/penggajian', [LaporanController::class, 'penggajian'])->name('laporan.penggajian');

    Route::get('export-database', [BackupDatabaseExport::class, 'export'])->name('export-database');
});

Route::get('/redirect', [SocialiteController::class, 'redirect'])->name('redirect')->middleware('guest');
Route::get('/callback', [SocialiteController::class, 'callback'])->name('callback')->middleware('guest');
Route::get('/logout', [SocialiteController::class, 'logout'])->name('socialite.logout')->middleware('auth');

Route::group(['prefix' => 'user', 'middleware' => ['auth']], function () {
    Route::get('dashboard', function () {
        return view('user.dashboard.index');
    })->name('user.dashboard');

    Route::get('profile', function () {
        return view('user.profile.index');
    });

    Route::get('absensi', [AbsensiController::class, 'index'])->middleware('auth');
    Route::resource('absensi', AbsensiController::class);
    Route::post('absen-sakit', [AbsensiController::class, 'absenSakit'])->name('absensi.absenSakit');
    Route::post('absen-pulang', [AbsensiController::class, 'absenPulang'])->name('absensi.absenPulang');

    Route::get('penggajian', [PenggajianController::class, 'index1'])->name('penggajian.index1');
    Route::delete('penggajian/{id}', [PenggajianController::class, 'destroy1'])->name('penggajian.destroy1');

    Route::get('cuti', [CutisController::class, 'index'])->name('cuti.index');
    Route::post('cuti/store', [CutisController::class, 'store'])->name('cuti.store');
    Route::patch('cuti/update-status/{id}', [CutisController::class, 'updateStatus'])->name('cuti.updateStatus');
});
