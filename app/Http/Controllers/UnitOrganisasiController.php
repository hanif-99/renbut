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
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $perangkatDaerah = PerangkatDaerah::with(['unitOrganisasi' => function ($query) {
            $query->orderBy('kode', 'asc');
        }])->orderBy('nama', 'asc')->get();

        foreach ($perangkatDaerah as $pd) {
            $pd->unit_organisasi_count = $pd->unitOrganisasi->filter(function ($unit) {
                return $this->getCodeLevel($unit->kode) <= 3;
            })->count();
        }

        return view('unit_organisasi.index', compact('perangkatDaerah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $perangkatDaerah = PerangkatDaerah::all();
        return view('unit_organisasi.create', compact('perangkatDaerah'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(UnitOrganisasi $unitOrganisasi): View
    {
        return view('unit_organisasi.show', compact('unitOrganisasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitOrganisasi $unitOrganisasi): View
    {
        $perangkatDaerah = PerangkatDaerah::all();
        return view('unit_organisasi.edit', compact('unitOrganisasi', 'perangkatDaerah'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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
     */
    private function getCodeLevel($kode)
    {
        if (!$kode) return 0;
        return count(array_filter(explode('.', trim($kode))));
    }

    /**
     * API: Ambil unit organisasi untuk satu Perangkat Daerah
     */
    public function units(Request $request, $id): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 50);
            
            $allUnits = UnitOrganisasi::where('perangkat_daerah_id', $id)
                ->orderBy('kode', 'asc')
                ->get();

            $filteredUnits = $allUnits->filter(function ($unit) {
                return $this->getCodeLevel($unit->kode) <= 3;
            })->values();

            $page = (int) $request->query('page', 1);
            $total = $filteredUnits->count();
            $lastPage = ceil($total / $perPage) ?: 1;
            $items = $filteredUnits->slice(($page - 1) * $perPage, $perPage)->values();

            $items->each(function ($unit) {
                $unit->_level = $this->getCodeLevel($unit->kode);
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
            \Log::error('UnitOrganisasi units error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * API: Search units (server-side search)
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $q = trim((string) $request->query('q', ''));
            $perPage = (int) $request->query('per_page', 500);

            \Log::info('UnitOrganisasi search query: ' . $q);

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

            $query = UnitOrganisasi::with('perangkatDaerah')
                ->where(function ($w) use ($q) {
                    $w->where('nama', 'like', '%' . $q . '%')
                      ->orWhere('kode', 'like', '%' . $q . '%');
                });

            $allResults = $query->get();

            \Log::info('UnitOrganisasi search found: ' . $allResults->count() . ' results');

            $filteredResults = $allResults->filter(function ($unit) {
                return $this->getCodeLevel($unit->kode) <= 3;
            })->values();

            $filteredResults = $filteredResults->sortBy(function ($unit) {
                return $unit->perangkat_daerah_id . '_' . str_pad($unit->kode, 20, '0', STR_PAD_LEFT);
            })->values();

            $page = (int) $request->query('page', 1);
            $total = $filteredResults->count();
            $lastPage = ceil($total / $perPage) ?: 1;
            $items = $filteredResults->slice(($page - 1) * $perPage, $perPage)->values();

            $items->each(function ($unit) {
                $unit->_level = $this->getCodeLevel($unit->kode);
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
            \Log::error('UnitOrganisasi search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
