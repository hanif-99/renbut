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

// import export classes
use App\Exports\FormasiExport;
use App\Exports\GapAnalysisExport;

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
        // Get per_page parameter, default 10
        $perPage = request()->get('per_page', 10);
        // Validate per_page value
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $page = request()->get('page', 1);
        $skip = ($page - 1) * $perPage;

        // Get total count
        $totalCount = Jabatan::count();
        $totalPages = ceil($totalCount / $perPage);

        // Validate page
        if ($page < 1 || $page > $totalPages) {
            $page = 1;
        }

        // Get paginated data
        $jabatan = Jabatan::with('unitOrganisasi.perangkatDaerah')
            ->skip($skip)
            ->take($perPage)
            ->get()
            ->map(function ($j) {
                $j->gap = ((int) $j->b) - ((int) $j->k);
                return $j;
            });

        // Get all data for summary stats
        $jabatanAll = Jabatan::with('unitOrganisasi.perangkatDaerah')
            ->get()
            ->map(function ($j) {
                $j->gap = ((int) $j->b) - ((int) $j->k);
                return $j;
            });

        return view('laporan.gap-analysis', compact(
            'jabatan',
            'jabatanAll',
            'page',
            'totalPages',
            'perPage',
            'totalCount'
        ));
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
            new FormasiExport($formasi, $tahun),
            $filename
        );
    }

    /**
     * Export khusus untuk Gap Analysis (data K, B, Gap, Status)
     */
    public function exportGapExcel()
    {
        // ambil semua jabatan dengan relasi (sama seperti di view gapAnalysis)
        $jabatan = Jabatan::with('unitOrganisasi.perangkatDaerah')
            ->get()
            ->map(function ($j) {
                $j->gap = ((int) $j->b) - ((int) $j->k);
                return $j;
            });

        $filename = "Laporan_Gap_Analysis.xlsx";

        return Excel::download(
            new GapAnalysisExport($jabatan),
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
