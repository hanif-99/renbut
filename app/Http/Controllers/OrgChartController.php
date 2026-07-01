<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerangkatDaerah;
use App\Models\Jabatan;

class OrgChartController extends Controller
{
    /**
     * Tampilkan halaman organogram (bagan organisasi) beserta daftar PerangkatDaerah untuk filter.
     * Halaman ini bersifat publik (tidak ada auth check di route yang saya sarankan).
     */
    public function index()
    {
        $perangkats = PerangkatDaerah::orderBy('nama')->get();
        return view('organogram', compact('perangkats'));
    }

    /**
     * Kembalikan data graf (nodes + edges) dalam format Cytoscape.
     * Mendukung filter per-PerangkatDaerah: ?pd=ID
     */
    public function data(Request $request)
    {
        $pdId = $request->query('pd');

        $query = PerangkatDaerah::with(['unitOrganisasi' => function ($q) {
            $q->orderBy('nama')->with(['jabatan' => function ($qq) {
                $qq->orderBy('nama');
            }]);
        }])->orderBy('nama');

        if ($pdId) {
            $query->where('id', $pdId);
        }

        $perangkats = $query->get();

        $nodes = [];
        $edges = [];

        foreach ($perangkats as $pd) {
            $pdKey = 'pd-' . $pd->id;
            $nodes[] = [
                'data' => [
                    'id' => $pdKey,
                    'realId' => $pd->id,
                    'label' => $pd->nama,
                    'type' => 'perangkat_daerah'
                ]
            ];

            foreach ($pd->unitOrganisasi as $uo) {
                $uoKey = 'uo-' . $uo->id;
                $nodes[] = [
                    'data' => [
                        'id' => $uoKey,
                        'realId' => $uo->id,
                        'label' => $uo->nama,
                        'type' => 'unit_organisasi'
                    ]
                ];

                $edges[] = [
                    'data' => [
                        'id' => 'e-' . $pdKey . '-' . $uoKey,
                        'source' => $pdKey,
                        'target' => $uoKey
                    ]
                ];

                foreach ($uo->jabatan as $jab) {
                    $jabKey = 'jab-' . $jab->id;
                    $nodes[] = [
                        'data' => [
                            'id' => $jabKey,
                            'realId' => $jab->id,
                            'label' => $jab->nama,
                            'type' => 'jabatan'
                        ]
                    ];

                    $edges[] = [
                        'data' => [
                            'id' => 'e-' . $uoKey . '-' . $jabKey,
                            'source' => $uoKey,
                            'target' => $jabKey
                        ]
                    ];
                }
            }
        }

        // h apus duplikat berdasarkan id
        $seen = [];
        $elements = [];
        foreach (array_merge($nodes, $edges) as $el) {
            $key = $el['data']['id'] ?? json_encode($el);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $elements[] = $el;
            }
        }

        return response()->json($elements);
    }

    /**
     * Kembalikan detail sebuah jabatan (dipanggil saat klik node).
     * Route: /organogram/detail/{id}
     */
    public function detail($id)
    {
        // id adalah numeric jabatan id
        $jab = Jabatan::with('unitOrganisasi.perangkatDaerah')->find($id);

        if (! $jab) {
            return response()->json(['error' => 'Jabatan tidak ditemukan'], 404);
        }

        $data = [
            'id' => $jab->id,
            'kode' => $jab->kode,
            'nama' => $jab->nama,
            'kebutuhan' => (int) ($jab->k ?? 0),
            'bezetting' => (int) ($jab->b ?? 0),
            'gap' => ((int) ($jab->b ?? 0)) - ((int) ($jab->k ?? 0)), // B - K
            'unit' => $jab->unitOrganisasi->nama ?? null,
            'perangkat_daerah' => $jab->unitOrganisasi->perangkatDaerah->nama ?? null,
            // tambahkan fields lain jika diperlukan
        ];

        return response()->json($data);
    }
}