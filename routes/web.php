<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerangkatDaerahController;
use App\Http\Controllers\UnitOrganisasiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\FormasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OrgChartController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


// Main Application Routes (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perangkat Daerah
    Route::resource('perangkat_daerah', PerangkatDaerahController::class);

    // Unit Organisasi
    Route::resource('unit_organisasi', UnitOrganisasiController::class);

    // Jabatan
    Route::resource('jabatan', JabatanController::class);

    // Formasi ASN
    Route::get('/formasi/yearly-plan', [FormasiController::class, 'yearlyPlan'])->name('formasi.yearly-plan');
    Route::resource('formasi', FormasiController::class);

    // Laporan
    Route::get('/laporan/summary', [LaporanController::class, 'summary'])->name('laporan.summary');
    Route::get('/laporan/gap-analysis', [LaporanController::class, 'gapAnalysis'])->name('laporan.gap-analysis');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    // Export khusus Gap Analysis
    Route::get('/laporan/export-gap-excel', [LaporanController::class, 'exportGapExcel'])->name('laporan.export-gap-excel');

    // Organogram / Peta Jabatan (Protected - requires auth & verified)
    Route::get('/organogram', [OrgChartController::class, 'index'])->name('organogram.index');
    Route::get('/organogram/data', [OrgChartController::class, 'data'])->name('organogram.data');
    Route::get('/organogram/detail/{id}', [OrgChartController::class, 'detail'])->name('organogram.detail');
});