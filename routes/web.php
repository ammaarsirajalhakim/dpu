<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyProgressReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'indexUser'])->name('dashboard');

    Route::get('/penugasan', [PenugasanController::class, 'indexUser'])->name('penugasan.index');
    Route::get('/penugasan/{id}', [DailyProgressReportController::class, 'show'])->name('penugasan.show');

    Route::post('/penugasan/{id_penugasan}/laporan-harian', [DailyProgressReportController::class, 'store'])->name('daily-progress.store');
    Route::get('/laporan-harian/pending-summary', [DailyProgressReportController::class, 'pendingSummary'])->name('daily-progress.pending-summary');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/buat/{id_penugasan}', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/detail/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/perpanjangan/{id_penugasan}', [LaporanController::class, 'ajukanPerpanjangan'])->name('laporan.ajukanPerpanjangan');
    Route::post('/laporan/chat', [LaporanController::class, 'storeChat'])->name('laporan.chat.store');
    Route::post('/laporan/revisi/{id}', [LaporanController::class, 'submitRevisi'])->name('laporan.submitRevisi');

    Route::middleware(['role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifikasi/perpanjangan', [LaporanController::class, 'pendingExtensionSummary'])->name('extension.pending-summary');

        Route::prefix('pengguna')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/tambah', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{nip}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{nip}', [UserController::class, 'update'])->name('update');
            Route::delete('/{nip}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [TugasController::class, 'index'])->name('index');
            Route::get('/template', [TugasController::class, 'template'])->name('template');
            Route::get('/export', [TugasController::class, 'export'])->name('export');
            Route::get('/tambah', [TugasController::class, 'create'])->name('create');
            Route::post('/store', [TugasController::class, 'store'])->name('store');
            Route::post('/import-process', [TugasController::class, 'importProcess'])->name('importProcess');
            Route::delete('/{kodetugas}', [TugasController::class, 'destroy'])->name('destroy');
            Route::get('/{kodetugas}/edit', [TugasController::class, 'edit'])->name('edit');
            Route::put('/{kodetugas}', [TugasController::class, 'update'])->name('update');
            Route::get('/{kodetugas}', [TugasController::class, 'show'])->name('show');
        });

        Route::prefix('penugasan')->name('penugasan.')->group(function () {
            Route::get('/', [PenugasanController::class, 'index'])->name('index');
            Route::get('/template', [PenugasanController::class, 'template'])->name('template');
            Route::get('/export', [PenugasanController::class, 'export'])->name('export');
            Route::get('/tambah', [PenugasanController::class, 'create'])->name('create');
            Route::post('/store', [PenugasanController::class, 'store'])->name('store');
            Route::post('/import-process', [PenugasanController::class, 'importProcess'])->name('importProcess');
            Route::get('/check-existing/{kodetugas}', [PenugasanController::class, 'checkExisting'])->name('checkExisting');
            Route::get('/{id}', [PenugasanController::class, 'showAdmin'])->name('show');
            Route::get('/{id}/edit', [PenugasanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PenugasanController::class, 'update'])->name('update');
            Route::put('/{id}/deadline', [PenugasanController::class, 'updateDeadline'])->name('updateDeadline');
            Route::delete('/{id}', [PenugasanController::class, 'destroy'])->name('destroy');
        });

        Route::get('/manajemen-laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/detail/{id}', [LaporanController::class, 'showAdmin'])->name('laporan.show');
        Route::put('/laporan/{id}/status', [LaporanController::class, 'updateStatus'])->name('laporan.updateStatus');
        Route::post('/laporan/chat', [LaporanController::class, 'storeChat'])->name('laporan.chat.store');
    });
});
