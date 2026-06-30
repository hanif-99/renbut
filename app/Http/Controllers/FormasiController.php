<?php

namespace App\Http\Controllers;

use App\Models\FormasiAsn;
use App\Models\SisaKebutuhan2032;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FormasiController extends Controller
{
    public function index(): View
    {
        $tahun = request('tahun', 2027);
        $formasi = FormasiAsn::where('tahun', $tahun)
            ->with('jabatan.unitOrganisasi.perangkatDaerah')
            ->orderBy('jabatan_id')
            ->get();

        $tahunList = range(2027, 2032);

        return view('formasi.index', compact('formasi', 'tahun', 'tahunList'));
    }

    public function create(): View
    {
        $jabatan = Jabatan::all();
        $tahunList = range(2027, 2032);
        
        return view('formasi.create', compact('jabatan', 'tahunList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'tahun' => 'required|integer|between:2027,2032',
            'jpt' => 'required|integer|min:0',
            'adm_pengawas' => 'required|integer|min:0',
            'mutasi' => 'required|integer|min:0',
            'cpns' => 'required|integer|min:0',
            'pppk' => 'required|integer|min:0',
        ]);

        FormasiAsn::create($validated);

        // Update sisa kebutuhan 2032
        $this->updateSisaKebutuhan($validated['jabatan_id']);

        return redirect()->route('formasi.index')
            ->with('success', 'Formasi berhasil ditambahkan');
    }

    public function edit(FormasiAsn $formasi): View
    {
        $jabatan = Jabatan::all();
        $tahunList = range(2027, 2032);
        
        return view('formasi.edit', compact('formasi', 'jabatan', 'tahunList'));
    }

    public function update(Request $request, FormasiAsn $formasi): RedirectResponse
    {
        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'tahun' => 'required|integer|between:2027,2032',
            'jpt' => 'required|integer|min:0',
            'adm_pengawas' => 'required|integer|min:0',
            'mutasi' => 'required|integer|min:0',
            'cpns' => 'required|integer|min:0',
            'pppk' => 'required|integer|min:0',
        ]);

        $formasi->update($validated);

        // Update sisa kebutuhan 2032
        $this->updateSisaKebutuhan($validated['jabatan_id']);

        return redirect()->route('formasi.index')
            ->with('success', 'Formasi berhasil diperbarui');
    }

    public function destroy(FormasiAsn $formasi): RedirectResponse
    {
        $jabatanId = $formasi->jabatan_id;
        $formasi->delete();

        // Update sisa kebutuhan 2032
        $this->updateSisaKebutuhan($jabatanId);

        return redirect()->route('formasi.index')
            ->with('success', 'Formasi berhasil dihapus');
    }

    private function updateSisaKebutuhan($jabatanId): void
    {
        $totalFormasi = FormasiAsn::where('jabatan_id', $jabatanId)
            ->selectRaw('
                SUM(jpt) as jpt,
                SUM(adm_pengawas) as adm_pengawas,
                SUM(mutasi) as mutasi,
                SUM(cpns) as cpns,
                SUM(pppk) as pppk
            ')->first();

        SisaKebutuhan2032::updateOrCreate(
            ['jabatan_id' => $jabatanId],
            [
                'jpt' => $totalFormasi->jpt ?? 0,
                'adm_pengawas' => $totalFormasi->adm_pengawas ?? 0,
                'mutasi' => $totalFormasi->mutasi ?? 0,
                'cpns' => $totalFormasi->cpns ?? 0,
                'pppk' => $totalFormasi->pppk ?? 0,
            ]
        );
    }

    public function yearlyPlan(): View
    {
        $tahunList = range(2027, 2032);
        $data = [];

        foreach ($tahunList as $tahun) {
            $data[$tahun] = FormasiAsn::where('tahun', $tahun)
                ->selectRaw('
                    SUM(jpt) as jpt,
                    SUM(adm_pengawas) as adm_pengawas,
                    SUM(mutasi) as mutasi,
                    SUM(cpns) as cpns,
                    SUM(pppk) as pppk
                ')->first();
        }

        $sisaKebutuhan = SisaKebutuhan2032::selectRaw('
            SUM(jpt) as jpt,
            SUM(adm_pengawas) as adm_pengawas,
            SUM(mutasi) as mutasi,
            SUM(cpns) as cpns,
            SUM(pppk) as pppk
        ')->first();

        return view('formasi.yearly-plan', compact('tahunList', 'data', 'sisaKebutuhan'));
    }
}