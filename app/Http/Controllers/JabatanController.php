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

class JabatanController extends Controller
{
    private function getCodeLevel($kode)
    {
        if (!$kode) return 0;
        return count(array_filter(explode('.', trim($kode))));
    }

    /**
     * Display listing of Perangkat Daerah dengan unit count
     */
    public function index(): View
    {
        $perangkatDaerah = PerangkatDaerah::with(['unitOrganisasi' => function ($query) {
            $query->orderBy('kode', 'asc');
        }])->orderBy('nama', 'asc')->get();

        // Hitung unit & jabatan per perangkat
        foreach ($perangkatDaerah as $pd) {
            $unitIds = $pd->unitOrganisasi->pluck('id')->toArray();
            $pd->unit_count = $pd->unitOrganisasi->count();
            $pd->jabatan_count = Jabatan::whereIn('unit_organisasi_id', $unitIds)->count();
        }

        return view('jabatan.index', compact('perangkatDaerah'));
    }

    /**
     * UPDATED: API - Get Unit Organisasi untuk Perangkat (grouped if duplicates)
     */
    public function getUnitsByPerangkat(Request $request, $perangkatId): JsonResponse
    {
        try {
            $units = UnitOrganisasi::where('perangkat_daerah_id', $perangkatId)
                ->with('jabatan')
                ->orderBy('kode', 'asc')
                ->get();

            // Group units by (kode + nama) untuk eliminate duplicates
            $grouped = [];
            
            foreach ($units as $unit) {
                $key = $unit->kode . '|' . $unit->nama; // Unique key
                
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'id' => $unit->id,
                        'kode' => $unit->kode,
                        'nama' => $unit->nama,
                        'level' => $this->getCodeLevel($unit->kode),
                        'parent_id' => $unit->unor_atasan,
                        'unit_ids' => [$unit->id], // Track all unit IDs dengan kode+nama sama
                        'jabatan_count' => 0,
                    ];
                } else {
                    // Tambah unit ID ke daftar
                    $grouped[$key]['unit_ids'][] = $unit->id;
                }
            }

            // Hitung total jabatan dari semua unit yang sama
            foreach ($grouped as $key => $unitData) {
                $jabatanCount = Jabatan::whereIn('unit_organisasi_id', $unitData['unit_ids'])
                    ->count();
                $grouped[$key]['jabatan_count'] = $jabatanCount;
                // Hitung jumlah unit yang sama (duplicates)
                $grouped[$key]['duplicate_count'] = count($unitData['unit_ids']);
            }

            // Convert to array dengan index
            $organized = array_values($grouped);

            return response()->json([
                'success' => true,
                'data' => $organized,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get units by perangkat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data unit',
                'data' => []
            ], 500);
        }
    }

    /**
     * UPDATED: API - Get Jabatan untuk Unit (menangani multiple duplicates)
     */
    public function getJabatanByUnit(Request $request, $unitId): JsonResponse
    {
        try {
            // Ambil unit awal
            $unit = UnitOrganisasi::find($unitId);
            if (!$unit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan',
                ], 404);
            }

            // Cari semua unit dengan kode + nama yang sama
            $duplicateUnits = UnitOrganisasi::where('kode', $unit->kode)
                ->where('nama', $unit->nama)
                ->pluck('id')
                ->toArray();

            // Ambil semua jabatan dari semua unit yang sama
            $jabatan = Jabatan::whereIn('unit_organisasi_id', $duplicateUnits)
                ->orderBy('kode', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'unit_nama' => $unit->nama,
                'unit_kode' => $unit->kode,
                'duplicate_count' => count($duplicateUnits),
                'data' => $jabatan->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Get jabatan by unit error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jabatan',
            ], 500);
        }
    }

    /**
     * API: Get jabatan untuk satu perangkat daerah (DEPRECATED - kept for backward compatibility)
     */
    public function getJabatanByPerangkat(Request $request, $perangkatId): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 50);
            $page = (int) $request->query('page', 1);

            // Ambil semua unit dalam perangkat ini
            $units = UnitOrganisasi::where('perangkat_daerah_id', $perangkatId)
                ->orderBy('kode', 'asc')
                ->get();

            // Ambil semua jabatan dari unit-unit ini
            $unitIds = $units->pluck('id')->toArray();
            $allJabatan = Jabatan::whereIn('unit_organisasi_id', $unitIds)
                ->orderBy('kode', 'asc')
                ->get();

            // Group by unit
            $grouped = [];
            foreach ($allJabatan as $jab) {
                $unitNama = $jab->unitOrganisasi->nama ?? 'Unknown';
                if (!isset($grouped[$unitNama])) {
                    $grouped[$unitNama] = [];
                }
                $grouped[$unitNama][] = $jab;
            }

            // Pagination
            $total = count($grouped);
            $lastPage = ceil($total / $perPage) ?: 1;
            $items = array_slice($grouped, ($page - 1) * $perPage, $perPage, true);

            return response()->json([
                'success' => true,
                'data' => $items,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get jabatan by perangkat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * API: Search jabatan
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $q = trim((string) $request->query('q', ''));

            if (empty($q)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $jabatans = Jabatan::with(['unitOrganisasi.perangkatDaerah'])
                ->where(function ($query) use ($q) {
                    $query->where('jabatan.nama', 'like', '%' . $q . '%')
                          ->orWhere('jabatan.kode', 'like', '%' . $q . '%');
                })
                ->orderBy('jabatan.kode', 'asc')
                ->limit(500)
                ->get();

            // Group hasil
            $grouped = [];
            foreach ($jabatans as $jab) {
                $pdName = $jab->unitOrganisasi->perangkatDaerah->nama ?? 'Unknown';
                $unitName = $jab->unitOrganisasi->nama ?? 'Unknown';
                
                if (!isset($grouped[$pdName])) {
                    $grouped[$pdName] = [];
                }
                if (!isset($grouped[$pdName][$unitName])) {
                    $grouped[$pdName][$unitName] = [];
                }
                $grouped[$pdName][$unitName][] = $jab;
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