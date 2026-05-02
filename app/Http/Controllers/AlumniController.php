<?php

namespace App\Http\Controllers;

use App\Models\DataAlumni;
use App\Models\SertifikasiAlumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlumniController extends Controller
{
    // ── Dashboard Alumni ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $User    = Auth::user();
        $dataAlumni  = $User->dataAlumni;

        return view('alumni.dashboard', compact('User', 'dataAlumni'));
    }

    // ── Tampilkan Form Isi Data ──────────────────────────────────────────────

    public function tampilkanForm()
    {
        $User   = Auth::user();
        $dataAlumni = $User->dataAlumni;

        // Jika sudah diverifikasi, tidak bisa edit lagi
        if ($dataAlumni && $dataAlumni->sudah_diverifikasi) {
            return redirect()->route('alumni.dashboard')
                ->with('info', 'Data Anda sudah diverifikasi dan tidak dapat diubah.');
        }

        return view('alumni.form', compact('User', 'dataAlumni'));
    }

    // ── Simpan / Perbarui Form ───────────────────────────────────────────────

    public function simpanForm(Request $request)
    {
        $User = Auth::user();

        $validasi = $request->validate($this->aturanValidasi($request), $this->pesanValidasi());

        DB::transaction(function () use ($request, $User) {
            $dataAlumni = DataAlumni::updateOrCreate(
                ['User_id' => $User->id],
                array_merge($this->dataFormUtama($request), [
                    'diisi_pada'          => now(),
                    'sudah_diverifikasi'  => false,
                    'diverifikasi_oleh'   => null,
                    'diverifikasi_pada'   => null,
                ])
            );

            // Simpan sertifikasi (hapus lama, simpan baru)
            $dataAlumni->sertifikasi()->delete();
            if ($request->filled('sertifikasi')) {
                foreach ($request->sertifikasi as $sert) {
                    if (! empty($sert['nama_sertifikasi'])) {
                        $dataAlumni->sertifikasi()->create($sert);
                    }
                }
            }
        });

        return redirect()->route('alumni.dashboard')
            ->with('sukses', 'Data berhasil disimpan dan menunggu verifikasi admin.');
    }

    // ── Helper: Data Utama Form ──────────────────────────────────────────────

    private function dataFormUtama(Request $request): array
    {
        return [
            'nomor_induk_siswa'       => $request->nomor_induk_siswa,
            'nisn'                    => $request->nisn,
            'nama_lengkap'            => $request->nama_lengkap,
            'jenis_kelamin'           => $request->jenis_kelamin,
            'tempat_lahir'            => $request->tempat_lahir,
            'tanggal_lahir'           => $request->tanggal_lahir,
            'agama'                   => $request->agama,
            'status_pernikahan'       => $request->status_pernikahan,
            'nomor_telepon'           => $request->nomor_telepon,
            'email_aktif'             => $request->email_aktif,
            'alamat_sekarang'         => $request->alamat_sekarang,
            'kota_sekarang'           => $request->kota_sekarang,
            'provinsi_sekarang'       => $request->provinsi_sekarang,
            'kode_pos'                => $request->kode_pos,
            'jurusan_saat_sekolah'    => $request->jurusan_saat_sekolah,
            'tahun_masuk'             => $request->tahun_masuk,
            'tahun_lulus'             => $request->tahun_lulus,
            'status_kelulusan'        => $request->status_kelulusan,
            'status_saat_ini'         => $request->status_saat_ini,
            'lama_tunggu_bulan'       => $request->lama_tunggu_bulan,
            'nama_perusahaan'         => $request->nama_perusahaan,
            'jabatan'                 => $request->jabatan,
            'bidang_pekerjaan'        => $request->bidang_pekerjaan,
            'jenis_pekerjaan'         => $request->jenis_pekerjaan,
            'kesesuaian_bidang'       => $request->kesesuaian_bidang,
            'skala_gaji_bulanan'      => $request->skala_gaji_bulanan,
            'kota_bekerja'            => $request->kota_bekerja,
            'provinsi_bekerja'        => $request->provinsi_bekerja,
            'nama_perguruan_tinggi'   => $request->nama_perguruan_tinggi,
            'program_studi'           => $request->program_studi,
            'jenjang_kuliah'          => $request->jenjang_kuliah,
            'jalur_masuk_pt'          => $request->jalur_masuk_pt,
            'tahun_masuk_pt'          => $request->tahun_masuk_pt,
            'kota_perguruan_tinggi'   => $request->kota_perguruan_tinggi,
            'nilai_kompetensi'        => $request->nilai_kompetensi,
            'nilai_soft_skill'        => $request->nilai_soft_skill,
            'nilai_fasilitas_sekolah' => $request->nilai_fasilitas_sekolah,
            'nilai_kepuasan_sekolah'  => $request->nilai_kepuasan_sekolah,
            'saran_untuk_sekolah'     => $request->saran_untuk_sekolah,
        ];
    }

    // ── Aturan Validasi ──────────────────────────────────────────────────────

    private function aturanValidasi(Request $request): array
    {
        $bekerja  = in_array($request->status_saat_ini, ['bekerja', 'wirausaha', 'kuliah_dan_bekerja']);
        $kuliah   = in_array($request->status_saat_ini, ['kuliah', 'kuliah_dan_bekerja']);

        return [
            // Identitas
            'nomor_induk_siswa'       => ['required', 'string', 'max:20'],
            'nisn'                    => ['nullable', 'string', 'max:20'],
            'nama_lengkap'            => ['required', 'string', 'max:255'],
            'jenis_kelamin'           => ['required', 'in:laki-laki,perempuan'],
            'tempat_lahir'            => ['required', 'string', 'max:100'],
            'tanggal_lahir'           => ['required', 'date', 'before:today'],
            'agama'                   => ['nullable', 'string', 'max:50'],
            'status_pernikahan'       => ['required', 'in:belum_menikah,menikah,cerai'],
            // Kontak
            'nomor_telepon'           => ['required', 'string', 'max:20'],
            'email_aktif'             => ['required', 'email', 'max:255'],
            'alamat_sekarang'         => ['required', 'string'],
            'kota_sekarang'           => ['required', 'string', 'max:100'],
            'provinsi_sekarang'       => ['required', 'string', 'max:100'],
            'kode_pos'                => ['nullable', 'string', 'max:10'],
            // Riwayat Sekolah
            'jurusan_saat_sekolah'    => ['required', 'string', 'max:100'],
            'tahun_masuk'             => ['required', 'integer', 'min:1990', 'max:' . date('Y')],
            'tahun_lulus'             => ['required', 'integer', 'min:1990', 'max:' . date('Y'), 'gte:tahun_masuk'],
            'status_kelulusan'        => ['required', 'in:lulus,tidak_lulus,pindah'],
            // Status Saat Ini
            'status_saat_ini'         => ['required', 'in:bekerja,wirausaha,kuliah,kuliah_dan_bekerja,belum_bekerja,tidak_melanjutkan'],
            'lama_tunggu_bulan'       => ['nullable', 'integer', 'min:0', 'max:240'],
            // Pekerjaan (kondisional)
            'nama_perusahaan'         => [$bekerja ? 'required' : 'nullable', 'string', 'max:255'],
            'jabatan'                 => [$bekerja ? 'required' : 'nullable', 'string', 'max:255'],
            'jenis_pekerjaan'         => [$bekerja ? 'required' : 'nullable'],
            'kesesuaian_bidang'       => [$bekerja ? 'required' : 'nullable'],
            'skala_gaji_bulanan'      => [$bekerja ? 'required' : 'nullable'],
            // Kuliah (kondisional)
            'nama_perguruan_tinggi'   => [$kuliah ? 'required' : 'nullable', 'string', 'max:255'],
            'program_studi'           => [$kuliah ? 'required' : 'nullable', 'string', 'max:255'],
            'jenjang_kuliah'          => [$kuliah ? 'required' : 'nullable'],
            'tahun_masuk_pt'          => [$kuliah ? 'required' : 'nullable', 'nullable', 'integer'],
            // Penilaian
            'nilai_kompetensi'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'nilai_soft_skill'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'nilai_fasilitas_sekolah' => ['nullable', 'integer', 'min:1', 'max:5'],
            'nilai_kepuasan_sekolah'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'saran_untuk_sekolah'     => ['nullable', 'string'],
            // Sertifikasi
            'sertifikasi'             => ['nullable', 'array'],
            'sertifikasi.*.nama_sertifikasi' => ['required_with:sertifikasi', 'string', 'max:255'],
            'sertifikasi.*.lembaga_penerbit' => ['required_with:sertifikasi', 'string', 'max:255'],
            'sertifikasi.*.tahun_terbit'     => ['required_with:sertifikasi', 'integer'],
            'sertifikasi.*.kategori'         => ['nullable', 'string'],
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nomor_induk_siswa.required' => 'Nomor Induk Siswa wajib diisi.',
            'nama_lengkap.required'      => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required'     => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required'      => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'     => 'Tanggal lahir wajib diisi.',
            'nomor_telepon.required'     => 'Nomor telepon wajib diisi.',
            'email_aktif.required'       => 'Email aktif wajib diisi.',
            'alamat_sekarang.required'   => 'Alamat sekarang wajib diisi.',
            'kota_sekarang.required'     => 'Kota sekarang wajib diisi.',
            'provinsi_sekarang.required' => 'Provinsi wajib diisi.',
            'jurusan_saat_sekolah.required' => 'Jurusan wajib diisi.',
            'tahun_masuk.required'       => 'Tahun masuk wajib diisi.',
            'tahun_lulus.required'       => 'Tahun lulus wajib diisi.',
            'tahun_lulus.gte'            => 'Tahun lulus tidak boleh lebih kecil dari tahun masuk.',
            'status_saat_ini.required'   => 'Status saat ini wajib dipilih.',
        ];
    }
}
