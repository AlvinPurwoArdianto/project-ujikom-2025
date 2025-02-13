<?php

use App\Http\Controllers\Api\ApiAbsensiController;
use App\Http\Controllers\Api\ApiCutiController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    //route api absen
    Route::get('absensi', [ApiAbsensiController::class, 'index']);
    Route::post('absensi', [ApiAbsensiController::class, 'store']);
    Route::put('absensi/pulang/{id}', [ApiAbsensiController::class, 'update']);
    Route::post('absensi/sakit', [ApiAbsensiController::class, 'absenSakit']);
    Route::get('absensi/izin-sakit', [ApiAbsensiController::class, 'izinSakit']);
    Route::post('absensi/update-status', [ApiAbsensiController::class, 'absensiUpdateStatus']);
    Route::get('notifications', [ApiAbsensiController::class, 'getNotifications']);

    //route api cuti
    Route::get('cuti', [ApiCutiController::class, 'index']);
    Route::post('cuti', [ApiCutiController::class, 'store']);
    Route::put('cuti/approve/{id}', [ApiCutiController::class, 'approve']);
    Route::put('cuti/reject/{id}', [ApiCutiController::class, 'reject']);
    Route::get('cuti/notifications', [ApiCutiController::class, 'getNotifications']);

});
