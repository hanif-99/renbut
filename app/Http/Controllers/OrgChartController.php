<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerangkatDaerah;

class OrgChartController extends Controller
{
    /**
     * Tampilkan halaman organogram (bagan organisasi).
     */
    public function index()
    {
        return view('organogram');
    }

    /**
     * Kembalikan data graf (nodes + edges) dalam format Cytoscape.
     */
    public function data()
    {
        // Ambil PerangkatDaerah beserta UnitOrganisasi dan Jabatan terkait
        $perangkat = PerangkatDaerah::with('unitOrganisasi.jabatan')->get();

        $nodes = [];
        $edges = [];

        foreach ($perangkat as $pd) {
            $pdId = 'pd-' . $pd->id;
            $nodes[] = [
                'data' => [
                    'id' => $pdId,
                    'label' => $pd->nama,
                    'type' => 'perangkat_daerah'
                ]
            ];

            foreach ($pd->unitOrganisasi as $uo) {
                $uoId = 'uo-' . $uo->id;
                $nodes[] = [
                    'data' => [
                        'id' => $uoId,
                        'label' => $uo->nama,
                        'type' => 'unit_organisasi'
                    ]
                ];

                $edges[] = [
                    'data' => [
                        'id' => 'e-' . $pdId . '-' . $uoId,
                        'source' => $pdId,
                        'target' => $uoId
                    ]
                ];

                foreach ($uo->jabatan as $jab) {
                    $jabId = 'jab-' . $jab->id;
                    $nodes[] = [
                        'data' => [
                            'id' => $jabId,
                            'label' => $jab->nama,
                            'type' => 'jabatan'
                        ]
                    ];

                    $edges[] = [
                        'data' => [
                            'id' => 'e-' . $uoId . '-' . $jabId,
                            'source' => $uoId,
                            'target' => $jabId
                        ]
                    ];
                }
            }
        }

        // Hapus duplikasi nodes/edges bila ada (keamanan)
        $unique = [];
        $elements = [];

        foreach (array_merge($nodes, $edges) as $el) {
            $key = $el['data']['id'] ?? json_encode($el);
            if (!isset($unique[$key])) {
                $unique[$key] = true;
                $elements[] = $el;
            }
        }

        return response()->json($elements);
    }
}