<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PerangkatDaerahController;
use App\Http\Controllers\UnitOrganisasiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\FormasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/jabatan');
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

    // Jabatan Routes dengan Unit Hierarchy
    Route::get('perangkat_daerah/{id}/units', [JabatanController::class, 'getUnitsByPerangkat'])->name('jabatan.getUnits');
    Route::get('unit_organisasi/{id}/jabatan-list', [JabatanController::class, 'getJabatanByUnit'])->name('jabatan.getByUnitJson');
    Route::get('perangkat_daerah/{id}/jabatan', [JabatanController::class, 'getJabatanByPerangkat'])->name('jabatan.getByPerangkat');
    Route::get('jabatan/search', [JabatanController::class, 'search'])->name('jabatan.search');
    Route::resource('jabatan', JabatanController::class);

    // Formasi ASN
    Route::get('/formasi/yearly-plan', [FormasiController::class, 'yearlyPlan'])->name('formasi.yearly-plan');
    Route::resource('formasi', FormasiController::class);

    // Laporan
    Route::get('/laporan/summary', [LaporanController::class, 'summary'])->name('laporan.summary');
    Route::get('/laporan/gap-analysis', [LaporanController::class, 'gapAnalysis'])->name('laporan.gap-analysis');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    // Export Gap Analysis
    Route::get('/laporan/export-gap-excel', [LaporanController::class, 'exportGapExcel'])->name('laporan.export-gap-excel');

});