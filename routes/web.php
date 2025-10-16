<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KioskController;

Route::get('/', function () {
    return redirect()->route('kiosk.welcome');
});

// Kiosk Routes
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'welcome'])->name('welcome');
    Route::get('/phone', [KioskController::class, 'phoneInput'])->name('phone');
    Route::post('/phone', [KioskController::class, 'processPhone'])->name('phone.process');
    Route::get('/camera/{sessionId}', [KioskController::class, 'camera'])->name('camera');
    Route::post('/camera/{sessionId}', [KioskController::class, 'processPhoto'])->name('photo.process');
    Route::get('/preview/{sessionId}', [KioskController::class, 'preview'])->name('preview');
    Route::post('/confirm/{sessionId}', [KioskController::class, 'confirmPhoto'])->name('photo.confirm');
    Route::get('/processing/{sessionId}', [KioskController::class, 'processing'])->name('processing');
    Route::get('/status/{sessionId}', [KioskController::class, 'checkStatus'])->name('status');
    Route::get('/result/{sessionId}', [KioskController::class, 'result'])->name('result');
    Route::post('/retake/{sessionId}', [KioskController::class, 'retakePhoto'])->name('retake');
    Route::get('/new', [KioskController::class, 'newSession'])->name('new');
});
