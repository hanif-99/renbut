<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\UnitOrganisasi;
use App\Models\JenisJabatan;
use App\Models\Jenjang;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    public function index(Request $request): View
    {
        // Simple pagination - hanya load 10 perangkat per halaman
        $perPage = 10;
        $page = (int) $request->input('page', 1);
        
        // Count total perangkat
        $totalPerangkat = DB::table('perangkat_daerah')->count();
        
        // Fetch perangkat IDs untuk halaman ini (MINIMAL QUERY)
        $perangkatIds = DB::table('perangkat_daerah')
            ->orderBy('nama', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->pluck('id', 'nama')
            ->toArray();

        // Fetch semua data dalam 1-2 queries saja
        $unitsData = DB::table('unit_organisasi')
            ->whereIn('perangkat_daerah_id', array_keys($perangkatIds))
            ->select('id', 'nama', 'perangkat_daerah_id')
            ->get();

        $unitIds = $unitsData->pluck('id')->toArray();

        $jabatanData = DB::table('jabatan')
            ->whereIn('unit_organisasi_id', $unitIds)
            ->select('id', 'kode', 'nama', 'unit_organisasi_id', 'b', 'k')
            ->orderBy('kode', 'asc')
            ->get();

        // Group dalam PHP (lebih cepat daripada query)
        $groupedData = [];
        
        foreach ($perangkatIds as $pdNama => $pdId) {
            $units = $unitsData->where('perangkat_daerah_id', $pdId)
                ->groupBy('nama') // Group by nama unit (KONSISTEN)
                ->map(function($unitGroup) use ($jabatanData) {
                    $unitId = $unitGroup->first()->id;
                    $jabatan = $jabatanData->where('unit_organisasi_id', $unitId)->values();
                    return [
                        'nama' => $unitGroup->first()->nama,
                        'jabatan' => $jabatan->toArray()
                    ];
                })
                ->filter(fn($u) => count($u['jabatan']) > 0)
                ->values();

            if ($units->count() > 0) {
                $groupedData[] = [
                    'perangkat_nama' => $pdNama,
                    'units' => $units->toArray()
                ];
            }
        }

        $totalPages = ceil($totalPerangkat / $perPage);
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        return view('jabatan.index', compact(
            'groupedData',
            'page',
            'perPage',
            'totalPerangkat',
            'totalPages'
        ));
    }

    /**
     * API: Search jabatan dengan cache & limit
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $q = trim((string) $request->query('q', ''));

            if (empty($q) || strlen($q) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            // Raw query lebih cepat untuk search
            $jabatans = DB::table('jabatan')
                ->join('unit_organisasi', 'jabatan.unit_organisasi_id', '=', 'unit_organisasi.id')
                ->join('perangkat_daerah', 'unit_organisasi.perangkat_daerah_id', '=', 'perangkat_daerah.id')
                ->where('jabatan.nama', 'like', '%' . $q . '%')
                ->orWhere('jabatan.kode', 'like', '%' . $q . '%')
                ->select(
                    'jabatan.id',
                    'jabatan.kode',
                    'jabatan.nama',
                    'jabatan.b',
                    'jabatan.k',
                    'unit_organisasi.nama as unit_nama',
                    'perangkat_daerah.nama as perangkat_nama'
                )
                ->orderBy('jabatan.kode', 'asc')
                ->limit(500) // Maksimal 500 hasil
                ->get();

            // Group hasil
            $grouped = [];
            foreach ($jabatans as $jab) {
                if (!isset($grouped[$jab->perangkat_nama])) {
                    $grouped[$jab->perangkat_nama] = [];
                }
                if (!isset($grouped[$jab->perangkat_nama][$jab->unit_nama])) {
                    $grouped[$jab->perangkat_nama][$jab->unit_nama] = [];
                }
                $grouped[$jab->perangkat_nama][$jab->unit_nama][] = $jab;
            }

            return response()->json([
                'success' => true,
                'data' => $grouped,
                'total' => count($jabatans),
            ]);
        } catch (\Exception $e) {
            \Log::error('Jabatan search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian',
                'data' => []
            ], 500);
        }
    }

    public function create(): View
    {
        $unitOrganisasi = UnitOrganisasi::orderBy('nama')->get();
        $jenisJabatan = JenisJabatan::all();
        $jenjang = Jenjang::all();
        
        return view('jabatan.create', compact('unitOrganisasi', 'jenisJabatan', 'jenjang'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jabatan',
            'nama' => 'required|string|max:255',
            'unit_organisasi_id' => 'required|exists:unit_organisasi,id',
            'jenis_jabatan_id' => 'nullable|exists:jenis_jabatan,id',
            'jenjang_id' => 'nullable|exists:jenjang,id',
            'kj' => 'nullable|string|max:100',
            'b' => 'required|integer|min:0',
            'k' => 'required|integer|min:0',
        ]);

        Jabatan::create($validated);

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function edit(Jabatan $jabatan): View
    {
        $unitOrganisasi = UnitOrganisasi::orderBy('nama')->get();
        $jenisJabatan = JenisJabatan::all();
        $jenjang = Jenjang::all();
        
        return view('jabatan.edit', compact('jabatan', 'unitOrganisasi', 'jenisJabatan', 'jenjang'));
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jabatan,kode,' . $jabatan->id,
            'nama' => 'required|string|max:255',
            'unit_organisasi_id' => 'required|exists:unit_organisasi,id',
            'jenis_jabatan_id' => 'nullable|exists:jenis_jabatan,id',
            'jenjang_id' => 'nullable|exists:jenjang,id',
            'kj' => 'nullable|string|max:100',
            'b' => 'required|integer|min:0',
            'k' => 'required|integer|min:0',
        ]);

        $jabatan->update($validated);

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $jabatan->delete();

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }
}