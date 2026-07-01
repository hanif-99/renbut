<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Export untuk Gap Analysis
 * Input: collection of Jabatan models (dengan relasi unitOrganisasi.perangkatDaerah)
 * Output: kolom Perangkat Daerah, Unit Organisasi, Kode Jabatan, Nama Jabatan, K, B, Gap (B-K), Status
 */
class GapAnalysisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $jabatan;

    public function __construct($jabatan)
    {
        $this->jabatan = $jabatan;
    }

    public function collection()
    {
        return $this->jabatan;
    }

    public function map($jab): array
    {
        $unit = $jab->unitOrganisasi ?? null;
        $perangkat = $unit && $unit->perangkatDaerah ? $unit->perangkatDaerah->nama : '';
        $unitNama = $unit ? $unit->nama : '';
        $kode = $jab->kode ?? '';
        $nama = $jab->nama ?? '';

        $k = (int) ($jab->k ?? 0); // kebutuhan
        $b = (int) ($jab->b ?? 0); // bezetting
        $gap = $b - $k; // definisi: B - K (neg => kekurangan)

        $status = 'Terpenuhi';
        if ($gap < 0) $status = 'Kekurangan';
        elseif ($gap > 0) $status = 'Kelebihan';

        return [
            $perangkat,
            $unitNama,
            $kode,
            $nama,
            $k,
            $b,
            $gap,
            $status,
        ];
    }

    public function headings(): array
    {
        return [
            'Perangkat Daerah',
            'Unit Organisasi',
            'Kode Jabatan',
            'Nama Jabatan',
            'K (Kebutuhan)',
            'B (Bezetting)',
            'Gap (B - K)',
            'Status'
        ];
    }
}