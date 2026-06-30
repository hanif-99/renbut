<?php

namespace Database\Seeders;

use App\Models\JenisJabatan;
use App\Models\Jenjang;
use App\Models\PerangkatDaerah;
use App\Models\UnitOrganisasi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        // Seeder Jenis Jabatan
        $jenisJabatan = [
            'JPT (Jabatan Pimpinan Tinggi)',
            'Administrasi',
            'Pengawas',
            'Fungsional'
        ];

        foreach ($jenisJabatan as $jenis) {
            JenisJabatan::create(['nama' => $jenis]);
        }

        // Seeder Jenjang
        $jenjang = [
            'Eselon I',
            'Eselon II',
            'Eselon III',
            'Eselon IV',
            'Golongan I',
            'Golongan II',
            'Golongan III'
        ];

        foreach ($jenjang as $j) {
            Jenjang::create(['nama' => $j]);
        }

        // Seeder Sample Perangkat Daerah
        $perangkat = [
            ['kode' => 'PD001', 'nama' => 'Dinas Kesehatan'],
            ['kode' => 'PD002', 'nama' => 'Dinas Pendidikan'],
            ['kode' => 'PD003', 'nama' => 'Dinas Sosial'],
        ];

        foreach ($perangkat as $p) {
            PerangkatDaerah::create($p);
        }

        // Seeder Sample Unit Organisasi
        $unitOrganisasi = [
            ['kode' => 'UO001', 'nama' => 'Sekretariat Dinas Kesehatan', 'perangkat_daerah_id' => 1, 'unor_atasan' => 'Dinas Kesehatan'],
            ['kode' => 'UO002', 'nama' => 'Bidang Kesehatan Masyarakat', 'perangkat_daerah_id' => 1, 'unor_atasan' => 'Dinas Kesehatan'],
            ['kode' => 'UO003', 'nama' => 'Sekretariat Dinas Pendidikan', 'perangkat_daerah_id' => 2, 'unor_atasan' => 'Dinas Pendidikan'],
            ['kode' => 'UO004', 'nama' => 'Bidang Pendidikan Dasar', 'perangkat_daerah_id' => 2, 'unor_atasan' => 'Dinas Pendidikan'],
        ];

        foreach ($unitOrganisasi as $uo) {
            UnitOrganisasi::create($uo);
        }

        // Seeder Sample Jabatan
        $jabatan = [
            ['kode' => 'JAB001', 'nama' => 'Kepala Dinas', 'unit_organisasi_id' => 1, 'jenis_jabatan_id' => 1, 'jenjang_id' => 1, 'kj' => 'JPT', 'b' => 1, 'k' => 1],
            ['kode' => 'JAB002', 'nama' => 'Sekretaris Dinas', 'unit_organisasi_id' => 1, 'jenis_jabatan_id' => 2, 'jenjang_id' => 2, 'kj' => 'ASN', 'b' => 1, 'k' => 1],
            ['kode' => 'JAB003', 'nama' => 'Kepala Bidang', 'unit_organisasi_id' => 2, 'jenis_jabatan_id' => 3, 'jenjang_id' => 2, 'kj' => 'Pengawas', 'b' => 2, 'k' => 1],
            ['kode' => 'JAB004', 'nama' => 'Staf Administrasi', 'unit_organisasi_id' => 2, 'jenis_jabatan_id' => 2, 'jenjang_id' => 4, 'kj' => 'Umum', 'b' => 5, 'k' => 3],
        ];

        foreach ($jabatan as $jab) {
            Jabatan::create($jab);
        }

        $this->command->info('Database seeded successfully!');
    }
}