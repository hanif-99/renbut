<?php

namespace App\Http\Controllers;

use App\Models\UnitOrganisasi;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class UnitOrganisasiController extends Controller
{
    public function index(): View
    {
        // Ambil daftar Perangkat Daerah + jumlah Unit yang valid (max 3 level kode)
        $perangkatDaerah = PerangkatDaerah::with(['unitOrganisasi' => function ($query) {
            $query->get()->filter(function ($unit) {
                return $this->getCodeLevel($unit->kode) <= 3;
            });
        }])->orderBy('nama', 'asc')->get();

        // Hitung ulang unit_organisasi_count hanya yang memiliki kode <= 3 level
        foreach ($perangkatDaerah as $pd) {
            $pd->unit_organisasi_count = $pd->unitOrganisasi->filter(function ($unit) {
                return $this->getCodeLevel($unit->kode) <= 3;
            })->count();
        }

        return view('unit_organisasi.index', compact('perangkatDaerah'));
    }

    public function create(): View
    {
        $perangkatDaerah = PerangkatDaerah::all();
        return view('unit_organisasi.create', compact('perangkatDaerah'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:unit_organisasi',
            'nama' => 'required|string|max:255',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'unor_atasan' => 'nullable|string|max:255',
        ]);

        UnitOrganisasi::create($validated);

        return redirect()->route('unit_organisasi.index')
            ->with('success', 'Unit Organisasi berhasil ditambahkan');
    }

    public function edit(UnitOrganisasi $unitOrganisasi): View
    {
        $perangkatDaerah = PerangkatDaerah::all();
        return view('unit_organisasi.edit', compact('unitOrganisasi', 'perangkatDaerah'));
    }

    public function update(Request $request, UnitOrganisasi $unitOrganisasi): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:unit_organisasi,kode,' . $unitOrganisasi->id,
            'nama' => 'required|string|max:255',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'unor_atasan' => 'nullable|string|max:255',
        ]);

        $unitOrganisasi->update($validated);

        return redirect()->route('unit_organisasi.index')
            ->with('success', 'Unit Organisasi berhasil diperbarui');
    }

    public function destroy(Request $request, UnitOrganisasi $unitOrganisasi)
    {
        $unitOrganisasi->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit Organisasi berhasil dihapus']);
        }

        return redirect()->route('unit_organisasi.index')
            ->with('success', 'Unit Organisasi berhasil dihapus');
    }

    /**
     * Helper: Hitung jumlah level dalam kode
     * Contoh: "5.2.1" = 3 level, "5.2" = 2 level, "5" = 1 level
     */
    private function getCodeLevel($kode)
    {
        if (!$kode) return 0;
        return count(array_filter(explode('.', trim($kode))));
    }

    /**
     * API: Ambil unit organisasi untuk satu Perangkat Daerah
     * Filter: hanya kode dengan max 3 level (1.2.3)
     * GET /perangkat_daerah/{id}/units?per_page=50&page=1
     */
    public function units(Request $request, $id): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 50);
        
        // Ambil semua unit untuk PD ini, lalu filter di PHP
        $allUnits = UnitOrganisasi::where('perangkat_daerah_id', $id)
            ->orderBy('kode', 'asc')
            ->get();

        // Filter hanya kode dengan max 3 level
        $filteredUnits = $allUnits->filter(function ($unit) {
            return $this->getCodeLevel($unit->kode) <= 3;
        })->values();

        // Manual pagination
        $page = (int) $request->query('page', 1);
        $total = $filteredUnits->count();
        $lastPage = ceil($total / $perPage) ?: 1;
        $items = $filteredUnits->slice(($page - 1) * $perPage, $perPage)->values();

        // Tambahkan level info untuk setiap unit
        $items->each(function ($unit) {
            $unit->_level = $this->getCodeLevel($unit->kode);
        });

        return response()->json([
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
        ]);
    }

    /**
     * API: Search units (server-side search)
     * Filter: hanya kode dengan max 3 level
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 500);

        $query = UnitOrganisasi::with('perangkatDaerah');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('kode', 'like', '%' . $q . '%');
            });
        }

        $allResults = $query->get();

        // Filter hanya kode dengan max 3 level
        $filteredResults = $allResults->filter(function ($unit) {
            return $this->getCodeLevel($unit->kode) <= 3;
        })->values();

        // Sort by perangkat_daerah_id dan kode
        $filteredResults = $filteredResults->sortBy(function ($unit) {
            return $unit->perangkat_daerah_id . '_' . str_pad($unit->kode, 20, '0', STR_PAD_LEFT);
        })->values();

        // Manual pagination
        $page = (int) $request->query('page', 1);
        $total = $filteredResults->count();
        $lastPage = ceil($total / $perPage) ?: 1;
        $items = $filteredResults->slice(($page - 1) * $perPage, $perPage)->values();

        // Tambahkan level info
        $items->each(function ($unit) {
            $unit->_level = $this->getCodeLevel($unit->kode);
        });

        return response()->json([
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
        ]);
    }
}
