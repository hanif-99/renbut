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

class JabatanController extends Controller
{
    public function index(Request $request): View
    {
        // Get perpage parameter
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 20, 50])) {
            $perPage = 10;
        }

        // ✅ PAGINATION UNTUK PERFORMA OPTIMAL
        // Fetch perangkat daerah dengan pagination
        $perangkatDaerahs = PerangkatDaerah::with([
            'unitOrganisasi' => function ($query) {
                $query->with([
                    'jabatan' => function ($subQuery) {
                        $subQuery->select('id', 'kode', 'nama', 'unit_organisasi_id', 'b', 'k')
                                 ->orderBy('kode', 'asc');
                    }
                ])->orderBy('nama', 'asc');
            }
        ])
        ->orderBy('nama', 'asc')
        ->paginate($perPage);

        // Filter perangkat yang memiliki unit organisasi dan jabatan
        $groupedByPerangkat = [];
        foreach ($perangkatDaerahs as $perangkat) {
            $unitWithJabatan = [];
            foreach ($perangkat->unitOrganisasi as $unit) {
                if ($unit->jabatan->count() > 0) {
                    $unitWithJabatan[] = [
                        'unit' => $unit,
                        'jabatan' => $unit->jabatan
                    ];
                }
            }
            if (count($unitWithJabatan) > 0) {
                $groupedByPerangkat[] = [
                    'perangkat' => $perangkat,
                    'units' => $unitWithJabatan
                ];
            }
        }

        return view('jabatan.index', compact('groupedByPerangkat', 'perangkatDaerahs', 'perPage'));
    }

    public function create(): View
    {
        $unitOrganisasi = UnitOrganisasi::all();
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
        $unitOrganisasi = UnitOrganisasi::all();
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