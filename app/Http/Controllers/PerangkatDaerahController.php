<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PerangkatDaerahController extends Controller
{
    public function index(): View
    {
        $perangkat = PerangkatDaerah::all();
        return view('perangkat_daerah.index', compact('perangkat'));
    }

    public function create(): View
    {
        return view('perangkat_daerah.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:perangkat_daerah',
            'nama' => 'required|string|max:255',
        ]);

        PerangkatDaerah::create($validated);

        return redirect()->route('perangkat_daerah.index')
            ->with('success', 'Perangkat Daerah berhasil ditambahkan');
    }

    public function edit(PerangkatDaerah $perangkatDaerah): View
    {
        return view('perangkat_daerah.edit', compact('perangkatDaerah'));
    }

    public function update(Request $request, PerangkatDaerah $perangkatDaerah): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:perangkat_daerah,kode,' . $perangkatDaerah->id,
            'nama' => 'required|string|max:255',
        ]);

        $perangkatDaerah->update($validated);

        return redirect()->route('perangkat_daerah.index')
            ->with('success', 'Perangkat Daerah berhasil diperbarui');
    }

    public function destroy(PerangkatDaerah $perangkatDaerah): RedirectResponse
    {
        $perangkatDaerah->delete();

        return redirect()->route('perangkat_daerah.index')
            ->with('success', 'Perangkat Daerah berhasil dihapus');
    }
}