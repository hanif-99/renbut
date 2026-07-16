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
     * Get unique unit code (remove last digit - it's jabatan number)
     */
    private function getUnitCode($kode)
    {
        if (!$kode) return '';
        $parts = explode('.', trim($kode));
        if (count($parts) > 1) {
            array_pop($parts); // Remove last digit
            return implode('.', $parts);
        }
        return $kode;
    }

    /**
     * Display listing - Perangkat Daerah only
     */
    public function index(): View
    {
        $perangkatDaerah = PerangkatDaerah::orderBy('nama', 'asc')->get();

        foreach ($perangkatDaerah as $pd) {
            // Count UNIQUE unit names (tidak hitung duplikat)
            $uniqueUnitNames = UnitOrganisasi::where('perangkat_daerah_id', $pd->id)
                ->distinct('nama')
                ->count();
            
            $unitIds = UnitOrganisasi::where('perangkat_daerah_id', $pd->id)->pluck('id')->toArray();
            $pd->unit_count = $uniqueUnitNames;
            $pd->jabatan_count = Jabatan::whereIn('unit_organisasi_id', $unitIds)->count();
        }

        return view('jabatan.index', compact('perangkatDaerah'));
    }

    /**
     * API: Get UNIQUE Unit Organisasi untuk Perangkat (GROUP BY NAMA - NO DUPLICATES)
     */
    public function getUnitsByPerangkat(Request $request, $perangkatId): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 999);
            
            $allUnits = UnitOrganisasi::where('perangkat_daerah_id', $perangkatId)
                ->orderBy('nama', 'asc')
                ->get();

            // GROUP BY NAMA - eliminate duplicates completely
            $grouped = [];
            
            foreach ($allUnits as $unit) {
                $key = $unit->nama; // Group by NAMA only
                
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'id' => $unit->id,
                        'kode' => $unit->kode,
                        'nama' => $unit->nama,
                        '_level' => $this->getCodeLevel($unit->kode),
                        'unit_ids' => [$unit->id],
                        'jabatan_count' => 0,
                        'duplicate_count' => 1,
                    ];
                } else {
                    $grouped[$key]['unit_ids'][] = $unit->id;
                    $grouped[$key]['duplicate_count']++;
                }
            }

            // Hitung jabatan dari semua unit yang sama (by nama)
            foreach ($grouped as $key => $unitData) {
                $jabatanCount = Jabatan::whereIn('unit_organisasi_id', $unitData['unit_ids'])->count();
                $grouped[$key]['jabatan_count'] = $jabatanCount;
            }

            $organized = array_values($grouped);
            $page = (int) $request->query('page', 1);
            $total = count($organized);
            $lastPage = ceil($total / $perPage) ?: 1;
            $items = array_slice($organized, ($page - 1) * $perPage, $perPage);

            return response()->json([
                'success' => true,
                'data' => $items,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get units error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data unit',
                'data' => []
            ], 500);
        }
    }

    /**
     * API: Get Jabatan untuk Unit (berdasarkan NAMA unit)
     */
    public function getJabatanByUnit(Request $request, $unitId): JsonResponse
    {
        try {
            $unit = UnitOrganisasi::find($unitId);
            if (!$unit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan',
                ], 404);
            }

            // Cari semua unit dengan NAMA yang sama (ignore kode)
            $duplicateUnits = UnitOrganisasi::where('nama', $unit->nama)
                ->where('perangkat_daerah_id', $unit->perangkat_daerah_id)
                ->pluck('id')
                ->toArray();

            // Get all jabatan, grouped by unit KODE (excluding last digit)
            $allJabatan = Jabatan::whereIn('unit_organisasi_id', $duplicateUnits)
                ->orderBy('kode', 'asc')
                ->get();

            // Group by unit kode (excluding last digit)
            $grouped = [];
            foreach ($allJabatan as $jab) {
                $unitCode = $this->getUnitCode($jab->kode);
                if (!isset($grouped[$unitCode])) {
                    $grouped[$unitCode] = [];
                }
                $grouped[$unitCode][] = $jab->toArray();
            }

            return response()->json([
                'success' => true,
                'unit_nama' => $unit->nama,
                'unit_kode' => $unit->kode,
                'data' => $grouped,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get jabatan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jabatan',
            ], 500);
        }
    }

    /**
     * API: Search jabatan & unit organisasi
     * Jika query cocok dengan nama unit, tampilkan semua jabatan dari unit itu
     * Jika query cocok dengan jabatan, tampilkan hanya jabatan yang cocok
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $q = trim((string) $request->query('q', ''));
            $perPage = (int) $request->query('per_page', 500);

            if (empty($q)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'last_page' => 1,
                ]);
            }

            // Cari unit organisasi yang cocok dengan query
            $matchingUnits = UnitOrganisasi::where('nama', 'like', '%' . $q . '%')
                ->get();

            $jabatans = collect();

            // Jika ditemukan unit yang cocok, ambil semua jabatan dari unit tersebut
            if ($matchingUnits->count() > 0) {
                foreach ($matchingUnits as $unit) {
                    $unitIds = UnitOrganisasi::where('nama', $unit->nama)
                        ->where('perangkat_daerah_id', $unit->perangkat_daerah_id)
                        ->pluck('id')
                        ->toArray();

                    $unitJabatans = Jabatan::with(['unitOrganisasi.perangkatDaerah'])
                        ->whereIn('unit_organisasi_id', $unitIds)
                        ->orderBy('jabatan.kode', 'asc')
                        ->get();

                    $jabatans = $jabatans->merge($unitJabatans);
                }
            }

            // Tambahkan jabatan yang cocok dengan query (nama atau kode)
            // tapi tidak termasuk dalam unit yang sudah dicari
            $additionalJabatans = Jabatan::with(['unitOrganisasi.perangkatDaerah'])
                ->where(function ($query) use ($q) {
                    $query->where('jabatan.nama', 'like', '%' . $q . '%')
                          ->orWhere('jabatan.kode', 'like', '%' . $q . '%');
                })
                ->orderBy('jabatan.kode', 'asc')
                ->get();

            // Gabungkan hasil, hindari duplikat
            $allJabatans = $jabatans->merge($additionalJabatans)->unique('id')->values();

            $page = (int) $request->query('page', 1);
            $total = $allJabatans->count();
            $lastPage = ceil($total / $perPage) ?: 1;
            $items = $allJabatans->slice(($page - 1) * $perPage, $perPage)->values();

            $items->each(function ($jab) {
                $jab->_pd_name = $jab->unitOrganisasi->perangkatDaerah->nama ?? 'Unknown';
                $jab->_unit_name = $jab->unitOrganisasi->nama ?? 'Unknown';
                $jab->_level = $this->getCodeLevel($jab->kode);
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
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

    /**
     * API: Get unit hierarchy for form (create/edit page)
     * Return units grouped by NAMA (UNIQUE) - tidak ada duplikat
     */
    public function unitsHierarchy($perangkatId): JsonResponse
    {
        try {
            $units = UnitOrganisasi::where('perangkat_daerah_id', $perangkatId)
                ->orderBy('nama', 'asc')
                ->get();

            // Group by NAMA untuk eliminate duplicates
            $uniqueUnits = [];
            $seenNames = [];
            
            foreach ($units as $unit) {
                // Skip jika nama sudah pernah dilihat sebelumnya
                if (in_array($unit->nama, $seenNames)) {
                    continue;
                }
                $seenNames[] = $unit->nama;
                $uniqueUnits[] = $unit;
            }

            // Kemudian group by level
            $grouped = [];
            foreach ($uniqueUnits as $unit) {
                $level = $this->getCodeLevel($unit->kode);
                
                if (!isset($grouped[$level])) {
                    $grouped[$level] = [];
                }
                
                $grouped[$level][] = [
                    'id' => $unit->id,
                    'kode' => $unit->kode,
                    'nama' => $unit->nama,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $grouped,
            ]);
        } catch (\Exception $e) {
            \Log::error('Units hierarchy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data unit',
            ], 500);
        }
    }

    /**
     * Show the form for creating a new Jabatan
     */
    public function create(): View
    {
        $perangkatDaerah = PerangkatDaerah::orderBy('nama')->get();
        $jenisJabatan = JenisJabatan::all();
        $jenjang = Jenjang::all();
        
        return view('jabatan.create', compact('perangkatDaerah', 'jenisJabatan', 'jenjang'));
    }

    /**
     * Store a newly created Jabatan in storage
     */
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

    /**
     * Show the form for editing the specified Jabatan
     */
    public function edit(Jabatan $jabatan): View
    {
        $perangkatDaerah = PerangkatDaerah::orderBy('nama')->get();
        $jenisJabatan = JenisJabatan::all();
        $jenjang = Jenjang::all();
        
        return view('jabatan.edit', compact('jabatan', 'perangkatDaerah', 'jenisJabatan', 'jenjang'));
    }

    /**
     * Update the specified Jabatan in storage
     */
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

    /**
     * Remove the specified Jabatan from storage
     */
    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $jabatan->delete();
        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }
}
