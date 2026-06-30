<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDaerah;
use App\Models\UnitOrganisasi;
use App\Models\Jabatan;
use App\Models\JenisJabatan;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function show(): View
    {
        return view('import.excel');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $data = Excel::toArray(null, $file);

            // Parse dari sheet excel
            // Ini adalah template parsing untuk struktur yang Anda berikan
            $this->importData($data);

            return redirect()->back()
                ->with('success', 'Data berhasil diimport. Silakan verifikasi di master data.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function importData($data): void
    {
        // Parsing sesuai struktur Excel Anda
        // Adjust parsing logic sesuai kebutuhan
        
        foreach ($data[0] as $row) {
            if (empty($row[0])) continue;

            // Import Perangkat Daerah
            if (!empty($row[0]) && !empty($row[1])) {
                PerangkatDaerah::updateOrCreate(
                    ['kode' => $row[0]],
                    ['nama' => $row[1]]
                );
            }

            // Import Unit Organisasi
            if (!empty($row[3]) && !empty($row[0])) {
                $perangkat = PerangkatDaerah::where('kode', $row[0])->first();
                if ($perangkat) {
                    UnitOrganisasi::updateOrCreate(
                        ['kode' => $row[3] ?? ''],
                        [
                            'nama' => $row[3],
                            'perangkat_daerah_id' => $perangkat->id,
                            'unor_atasan' => $row[2] ?? null,
                        ]
                    );
                }
            }
        }
    }
}