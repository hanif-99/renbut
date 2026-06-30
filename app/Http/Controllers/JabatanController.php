<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\UnitOrganisasi;
use App\Models\JenisJabatan;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class JabatanController extends Controller
{
    public function index(): View
    {
        $jabatan = Jabatan::with('unitOrganisasi', 'jenisJabatan', 'jenjang')->get();
        return view('jabatan.index', compact('jabatan'));
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