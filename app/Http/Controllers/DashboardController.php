<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDaerah;
use App\Models\UnitOrganisasi;
use App\Models\Jabatan;
use App\Models\FormasiAsn;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPerangkat = PerangkatDaerah::count();
        $totalUnor = UnitOrganisasi::count();
        $totalJabatan = Jabatan::count();

        $formasiSummary = FormasiAsn::selectRaw('
            SUM(jpt) as total_jpt,
            SUM(adm_pengawas) as total_adm,
            SUM(mutasi) as total_mutasi,
            SUM(cpns) as total_cpns,
            SUM(pppk) as total_pppk
        ')->first();

        $formasiByTahun = FormasiAsn::selectRaw('
            tahun,
            SUM(jpt) as jpt,
            SUM(adm_pengawas) as adm_pengawas,
            SUM(mutasi) as mutasi,
            SUM(cpns) as cpns,
            SUM(pppk) as pppk
        ')->groupBy('tahun')
        ->orderBy('tahun')
        ->get();

        return view('dashboard', [
            'totalPerangkat' => $totalPerangkat,
            'totalUnor' => $totalUnor,
            'totalJabatan' => $totalJabatan,
            'formasiSummary' => $formasiSummary,
            'formasiByTahun' => $formasiByTahun,
        ]);
    }
}