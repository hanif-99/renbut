<?php

namespace App\Http\Controllers;

use App\Models\FormasiAsn;
use App\Models\SisaKebutuhan2032;
use App\Models\Jabatan;
use App\Models\PerangkatDaerah;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function summary(): View
    {
        $tahun = request('tahun', 2027);
        $tahunList = range(2027, 2032);

        $summary = FormasiAsn::where('tahun', $tahun)
            ->selectRaw('
                SUM(jpt) as jpt,
                SUM(adm_pengawas) as adm_pengawas,
                SUM(mutasi) as mutasi,
                SUM(cpns) as cpns,
                SUM(pppk) as pppk
            ')->first();

        $detailByPerangkat = FormasiAsn::where('tahun', $tahun)
            ->with('jabatan.unitOrganisasi.perangkatDaerah')
            ->get()
            ->groupBy('jabatan.unitOrganisasi.perangkatDaerah.id');

        return view('laporan.summary', compact('tahun', 'tahunList', 'summary', 'detailByPerangkat'));
    }

    public function gapAnalysis(): View
    {
        $jabatan = Jabatan::with('unitOrganisasi.perangkatDaerah')
            ->get()
            ->map(function ($j) {
                $j->gap = $j->b - $j->k;
                return $j;
            });

        return view('laporan.gap-analysis', compact('jabatan'));
    }

    public function exportExcel()
    {
        $tahun = request('tahun', 2027);
        
        $formasi = FormasiAsn::where('tahun', $tahun)
            ->with('jabatan.unitOrganisasi.perangkatDaerah', 'jabatan.jenisJabatan', 'jabatan.jenjang')
            ->orderBy('jabatan_id')
            ->get();

        $filename = "Laporan_Formasi_ASN_{$tahun}.xlsx";
        
        return Excel::download(
            new \App\Exports\FormasiExport($formasi, $tahun),
            $filename
        );
    }

    public function exportPdf()
    {
        $tahun = request('tahun', 2027);
        
        $summary = FormasiAsn::where('tahun', $tahun)
            ->selectRaw('
                SUM(jpt) as jpt,
                SUM(adm_pengawas) as adm_pengawas,
                SUM(mutasi) as mutasi,
                SUM(cpns) as cpns,
                SUM(pppk) as pppk
            ')->first();

        $formasi = FormasiAsn::where('tahun', $tahun)
            ->with('jabatan.unitOrganisasi.perangkatDaerah')
            ->orderBy('jabatan_id')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('tahun', 'summary', 'formasi'));
        return $pdf->download("Laporan_Formasi_ASN_{$tahun}.pdf");
    }
}