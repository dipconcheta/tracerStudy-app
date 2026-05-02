<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataAlumni;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AlumniAdminController extends Controller
{
    // ── Daftar Alumni ────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = DataAlumni::with('User')->sudahDiisi();

        // Filter pencarian
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%$cari%")
                  ->orWhere('nomor_induk_siswa', 'like', "%$cari%")
                  ->orWhere('nisn', 'like', "%$cari%");
            });
        }

        if ($request->filled('tahun_lulus')) {
            $query->where('tahun_lulus', $request->tahun_lulus);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan_saat_sekolah', $request->jurusan);
        }

        if ($request->filled('status')) {
            $query->where('status_saat_ini', $request->status);
        }

        if ($request->filled('verifikasi')) {
            $query->where('sudah_diverifikasi', $request->verifikasi === 'ya');
        }

        $alumniList    = $query->latest('diisi_pada')->paginate(20)->withQueryString();
        $tahunTersedia = DataAlumni::sudahDiisi()->select('tahun_lulus')->distinct()->orderBy('tahun_lulus')->pluck('tahun_lulus');
        $jurusanList   = DataAlumni::sudahDiisi()->select('jurusan_saat_sekolah')->distinct()->orderBy('jurusan_saat_sekolah')->pluck('jurusan_saat_sekolah');

        return view('admin.alumni.index', compact('alumniList', 'tahunTersedia', 'jurusanList'));
    }

    // ── Detail Alumni ────────────────────────────────────────────────────────

    public function tampilkan(DataAlumni $alumni)
    {
        $alumni->load('User', 'sertifikasi', 'verifikator');
        return view('admin.alumni.tampilkan', compact('alumni'));
    }

    // ── Verifikasi Data ──────────────────────────────────────────────────────

    public function verifikasi(DataAlumni $alumni)
    {
        $alumni->update([
            'sudah_diverifikasi' => true,
            'diverifikasi_oleh'  => Auth::id(),
            'diverifikasi_pada'  => now(),
        ]);

        return back()->with('sukses', "Data {$alumni->nama_lengkap} berhasil diverifikasi.");
    }

    public function batalkanVerifikasi(DataAlumni $alumni)
    {
        $alumni->update([
            'sudah_diverifikasi' => false,
            'diverifikasi_oleh'  => null,
            'diverifikasi_pada'  => null,
        ]);

        return back()->with('sukses', "Verifikasi data {$alumni->nama_lengkap} dibatalkan.");
    }

    // ── Kelola Akun Alumni ───────────────────────────────────────────────────

    public function daftarAkun(Request $request)
    {
        $query = User::where('peran', 'alumni')->with('dataAlumni');

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%$cari%")
                  ->orWhere('email', 'like', "%$cari%");
            });
        }

        $akunList = $query->latest()->paginate(20)->withQueryString();
        return view('admin.alumni.akun', compact('akunList'));
    }

    // ── Buat Akun Alumni (Manual oleh Admin) ─────────────────────────────────

    public function buatAkun(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:User,email'],
            'kata_sandi'   => ['required', 'min:8'],
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'kata_sandi'   => Hash::make($request->kata_sandi),
            'peran'        => 'alumni',
        ]);

        return back()->with('sukses', 'Akun alumni berhasil dibuat.');
    }

    // ── Toggle Aktif/Nonaktif Akun ────────────────────────────────────────────

    public function toggleAktif(User $User)
    {
        if ($User->isAdmin()) {
            return back()->with('gagal', 'Tidak dapat menonaktifkan akun admin.');
        }

        $User->update(['aktif' => ! $User->aktif]);
        $status = $User->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('sukses', "Akun {$User->nama_lengkap} berhasil $status.");
    }

    // ── Ekspor Data (CSV) ─────────────────────────────────────────────────────

    public function ekspor(Request $request)
    {
        $data = DataAlumni::sudahDiisi()
            ->when($request->tahun_lulus, fn($q) => $q->where('tahun_lulus', $request->tahun_lulus))
            ->when($request->jurusan, fn($q) => $q->where('jurusan_saat_sekolah', $request->jurusan))
            ->get();

        $header = [
            'NIS', 'NISN', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
            'Jurusan', 'Tahun Masuk', 'Tahun Lulus', 'Status Saat Ini',
            'Nama Perusahaan', 'Jabatan', 'Gaji', 'Nama PT', 'Program Studi',
            'Kepuasan (1-5)', 'Diisi Pada', 'Terverifikasi',
        ];

        $baris = $data->map(fn($a) => [
            $a->nomor_induk_siswa,
            $a->nisn,
            $a->nama_lengkap,
            $a->jenis_kelamin,
            $a->tempat_lahir,
            $a->tanggal_lahir?->format('d/m/Y'),
            $a->jurusan_saat_sekolah,
            $a->tahun_masuk,
            $a->tahun_lulus,
            $a->labelStatusSaatIni(),
            $a->nama_perusahaan,
            $a->jabatan,
            $a->skala_gaji_bulanan,
            $a->nama_perguruan_tinggi,
            $a->program_studi,
            $a->nilai_kepuasan_sekolah,
            $a->diisi_pada?->format('d/m/Y H:i'),
            $a->sudah_diverifikasi ? 'Ya' : 'Tidak',
        ]);

        $namaFile = 'tracer-study-' . now()->format('Ymd-His') . '.csv';

        $callback = function () use ($header, $baris) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);
            foreach ($baris as $b) {
                fputcsv($file, $b);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$namaFile\"",
        ]);
    }
}
