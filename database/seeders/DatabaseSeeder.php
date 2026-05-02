<?php

namespace Database\Seeders;

use App\Models\DataAlumni;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Akun Admin ───────────────────────────────────────────────────────
        User::create([
            'nama_lengkap' => 'Administrator',
            'email'        => 'admin@sekolah.sch.id',
            'kata_sandi'   => Hash::make('admin1234'),
            'peran'        => 'admin',
            'aktif'        => true,
        ]);

        // ── Akun Alumni Contoh ───────────────────────────────────────────────
        $alumniData = [
            [
                'nama_lengkap' => 'Justinus Lhaksana',
                'email'        => 'justin@email.com',
                'nis'          => '20001001',
                'jurusan'      => 'Teknik Komputer dan Jaringan',
                'tahun_masuk'  => 2017,
                'tahun_lulus'  => 2020,
                'status'       => 'bekerja',
                'perusahaan'   => 'PT Telkom Indonesia',
                'gaji'         => '3jt_5jt',
            ],
            [
                'nama_lengkap' => 'Emmabel',
                'email'        => 'emmabel@email.com',
                'nis'          => '20001002',
                'jurusan'      => 'Akuntansi',
                'tahun_masuk'  => 2017,
                'tahun_lulus'  => 2020,
                'status'       => 'kuliah',
                'pt'           => 'Universitas Indonesia',
                'prodi'        => 'Akuntansi',
            ],
            [
                'nama_lengkap' => 'Aaron Nicky',
                'email'        => 'aaron@email.com',
                'nis'          => '20001003',
                'jurusan'      => 'Teknik Komputer dan Jaringan',
                'tahun_masuk'  => 2018,
                'tahun_lulus'  => 2021,
                'status'       => 'wirausaha',
                'perusahaan'   => 'Toko Online AF',
                'gaji'         => '2jt_3jt',
            ],
        ];

        foreach ($alumniData as $a) {
            $User = User::create([
                'nama_lengkap' => $a['nama_lengkap'],
                'email'        => $a['email'],
                'kata_sandi'   => Hash::make('alumni1234'),
                'peran'        => 'alumni',
                'aktif'        => true,
            ]);

            DataAlumni::create([
                'User_id'          => $User->id,
                'nomor_induk_siswa'    => $a['nis'],
                'nama_lengkap'         => $a['nama_lengkap'],
                'jenis_kelamin'        => 'laki-laki',
                'tempat_lahir'         => 'Palembang',
                'tanggal_lahir'        => '2000-01-01',
                'nomor_telepon'        => '08123456789',
                'email_aktif'          => $a['email'],
                'alamat_sekarang'      => 'Jl. situ No. 1',
                'kota_sekarang'        => 'Palembang',
                'provinsi_sekarang'    => 'Sumatera Selatan',
                'jurusan_saat_sekolah' => $a['jurusan'],
                'tahun_masuk'          => $a['tahun_masuk'],
                'tahun_lulus'          => $a['tahun_lulus'],
                'status_kelulusan'     => 'lulus',
                'status_saat_ini'      => $a['status'],
                'nama_perusahaan'      => $a['perusahaan'] ?? null,
                'jabatan'              => isset($a['perusahaan']) ? 'Staff' : null,
                'jenis_pekerjaan'      => $a['status'] === 'wirausaha' ? 'wirausaha' : ($a['status'] === 'bekerja' ? 'swasta' : null),
                'kesesuaian_bidang'    => isset($a['perusahaan']) ? 'sesuai' : null,
                'skala_gaji_bulanan'   => $a['gaji'] ?? null,
                'kota_bekerja'         => isset($a['perusahaan']) ? 'Palembang' : null,
                'provinsi_bekerja'     => isset($a['perusahaan']) ? 'Sumatera Selatan' : null,
                'nama_perguruan_tinggi' => $a['pt'] ?? null,
                'program_studi'        => $a['prodi'] ?? null,
                'jenjang_kuliah'       => isset($a['pt']) ? 's1' : null,
                'nilai_kompetensi'     => rand(3, 5),
                'nilai_soft_skill'     => rand(3, 5),
                'nilai_fasilitas_sekolah' => rand(3, 5),
                'nilai_kepuasan_sekolah'  => rand(3, 5),
                'diisi_pada'           => now(),
                'sudah_diverifikasi'   => false,
            ]);
        }
    }
}
