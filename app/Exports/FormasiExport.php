<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Export class untuk Laporan Formasi ASN
 *
 * Diterima konstruktor: collection $formasi (FormasiAsn models) dan $tahun (opsional)
 * Menghasilkan file Excel dengan kolom:
 * Perangkat Daerah, Unit Organisasi, Kode Jabatan, Nama Jabatan,
 * JPT, ADM & Pengawas, Mutasi, CPNS, PPPK, Total
 */
class FormasiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $formasi;
    protected $tahun;

    public function __construct($formasi, $tahun = null)
    {
        // $formasi diharapkan sudah berisi koleksi FormasiAsn dengan relasi jabatan.unitOrganisasi.perangkatDaerah
        $this->formasi = $formasi;
        $this->tahun = $tahun;
    }

    /**
     * Mengembalikan collection yang akan diexport.
     * WithMapping akan memetakan setiap item menjadi baris array.
     */
    public function collection()
    {
        // Pastikan mengembalikan koleksi (Illuminate\Support\Collection)
        return $this->formasi;
    }

    /**
     * Map setiap model FormasiAsn menjadi array baris untuk Excel.
     */
    public function map($formasi): array
    {
        $jabatan = $formasi->jabatan ?? null;
        $perangkat = $jabatan->unitOrganisasi->perangkatDaerah->nama ?? '';
        $unit = $jabatan->unitOrganisasi->nama ?? '';
        $kode = $jabatan->kode ?? '';
        $namaJabatan = $jabatan->nama ?? '';

        $jpt = (int) ($formasi->jpt ?? 0);
        $adm = (int) ($formasi->adm_pengawas ?? 0);
        $mutasi = (int) ($formasi->mutasi ?? 0);
        $cpns = (int) ($formasi->cpns ?? 0);
        $pppk = (int) ($formasi->pppk ?? 0);
        $total = $jpt + $adm + $mutasi + $cpns + $pppk;

        return [
            $perangkat,
            $unit,
            $kode,
            $namaJabatan,
            $jpt,
            $adm,
            $mutasi,
            $cpns,
            $pppk,
            $total,
        ];
    }

    /**
     * Headings kolom Excel.
     */
    public function headings(): array
    {
        return [
            'Perangkat Daerah',
            'Unit Organisasi',
            'Kode Jabatan',
            'Nama Jabatan',
            'JPT',
            'ADM & Pengawas',
            'Mutasi',
            'CPNS',
            'PPPK',
            'Total'
        ];
    }
}