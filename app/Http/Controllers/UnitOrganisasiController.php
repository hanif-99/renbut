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
        // Ambil daftar Perangkat Daerah + jumlah Unit (tidak memuat semua unit)
        $perangkatDaerah = PerangkatDaerah::withCount('unitOrganisasi')
            ->orderBy('nama', 'asc')
            ->get();

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

        // Jika request AJAX: kembalikan JSON, else redirect
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit Organisasi berhasil dihapus']);
        }

        return redirect()->route('unit_organisasi.index')
            ->with('success', 'Unit Organisasi berhasil dihapus');
    }

    /**
     * API: Ambil unit organisasi untuk satu Perangkat Daerah (paginated)
     * GET /perangkat_daerah/{id}/units?per_page=50&page=1
     */
    public function units(Request $request, $id): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 50);
        $units = UnitOrganisasi::where('perangkat_daerah_id', $id)
            ->orderBy('nama')
            ->paginate($perPage);

        return response()->json($units);
    }

    /**
     * API: Search units (server-side search) — returns paginated results with perangkat_daerah relation
     * GET /unit_organisasi/search?q=term&per_page=50&page=1
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 50);

        $query = UnitOrganisasi::with('perangkatDaerah');

        if ($q !== '') {
            // Simple LIKE search — untuk dataset besar pertimbangkan fulltext/search engine
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('kode', 'like', '%' . $q . '%');
            });
        }

        $result = $query->orderBy('nama')->paginate($perPage);

        return response()->json($result);
    }
}