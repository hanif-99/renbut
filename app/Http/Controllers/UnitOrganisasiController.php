<?php

namespace App\Http\Controllers;

use App\Models\UnitOrganisasi;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UnitOrganisasiController extends Controller
{
    public function index(): View
    {
        $unitOrganisasi = UnitOrganisasi::with('perangkatDaerah')->get();
        return view('unit_organisasi.index', compact('unitOrganisasi'));
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

    public function destroy(UnitOrganisasi $unitOrganisasi): RedirectResponse
    {
        $unitOrganisasi->delete();

        return redirect()->route('unit_organisasi.index')
            ->with('success', 'Unit Organisasi berhasil dihapus');
    }
}