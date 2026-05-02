<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataAlumni;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    // ── Dashboard Utama ──────────────────────────────────────────────────────

    public function index()
    {
        $statistik = $this->ambilStatistikUtama();
        $grafik    = $this->ambilDataGrafik();

        return view('admin.dashboard.index', compact('statistik', 'grafik'));
    }

    // ── API: Data Grafik (JSON untuk Chart.js) ───────────────────────────────

    public function dataGrafikJson(Request $request)
    {
        return response()->json($this->ambilDataGrafik($request->get('tahun_lulus')));
    }

    // ── Statistik Ringkasan ──────────────────────────────────────────────────

    private function ambilStatistikUtama(): array
    {
        $totalAlumni      = User::where('peran', 'alumni')->count();
        $sudahMengisi     = DataAlumni::sudahDiisi()->count();
        $belumVerifikasi  = DataAlumni::belumDiverifikasi()->count();
        $persentaseIsi    = $totalAlumni > 0
            ? round(($sudahMengisi / $totalAlumni) * 100, 1)
            : 0;

        $rataKepuasan = DataAlumni::sudahDiisi()
            ->whereNotNull('nilai_kepuasan_sekolah')
            ->avg('nilai_kepuasan_sekolah');

        return [
            'total_alumni'       => $totalAlumni,
            'sudah_mengisi'      => $sudahMengisi,
            'belum_mengisi'      => $totalAlumni - $sudahMengisi,
            'belum_verifikasi'   => $belumVerifikasi,
            'persentase_isi'     => $persentaseIsi,
            'rata_kepuasan'      => round($rataKepuasan ?? 0, 2),
        ];
    }

    // ── Data Grafik Lengkap ──────────────────────────────────────────────────

    private function ambilDataGrafik(?int $tahunFilter = null): array
    {
        $query = DataAlumni::sudahDiisi();
        if ($tahunFilter) {
            $query->where('tahun_lulus', $tahunFilter);
        }

        return [
            'status_saat_ini'    => $this->grafikStatusSaatIni($tahunFilter),
            'jurusan'            => $this->grafikPerJurusan($tahunFilter),
            'tahun_lulus'        => $this->grafikPerTahunLulus(),
            'gaji'               => $this->grafikDistribusiGaji($tahunFilter),
            'jenis_kelamin'      => $this->grafikJenisKelamin($tahunFilter),
            'kesesuaian_bidang'  => $this->grafikKesesuaianBidang($tahunFilter),
            'jenis_pt'           => $this->grafikJenjangKuliah($tahunFilter),
            'lama_tunggu'        => $this->grafikLamaTunggu($tahunFilter),
            'nilai_rata'         => $this->grafikNilaiRata($tahunFilter),
            'provinsi_kerja'     => $this->grafikProvinsiKerja($tahunFilter),
            'tahun_tersedia'     => DataAlumni::sudahDiisi()
                                    ->select('tahun_lulus')
                                    ->distinct()
                                    ->orderBy('tahun_lulus')
                                    ->pluck('tahun_lulus'),
        ];
    }

    // ── Grafik: Status Saat Ini ──────────────────────────────────────────────

    private function grafikStatusSaatIni(?int $tahun): array
    {
        $labelMap = [
            'bekerja'            => 'Bekerja',
            'wirausaha'          => 'Wirausaha',
            'kuliah'             => 'Kuliah',
            'kuliah_dan_bekerja' => 'Kuliah & Bekerja',
            'belum_bekerja'      => 'Belum Bekerja',
            'tidak_melanjutkan'  => 'Tidak Melanjutkan',
        ];

        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->select('status_saat_ini', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status_saat_ini')
            ->get();

        return [
            'labels' => $data->map(fn($d) => $labelMap[$d->status_saat_ini] ?? $d->status_saat_ini)->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Per Jurusan ──────────────────────────────────────────────────

    private function grafikPerJurusan(?int $tahun): array
    {
        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->select('jurusan_saat_sekolah', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('jurusan_saat_sekolah')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('jurusan_saat_sekolah')->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Per Tahun Lulus ──────────────────────────────────────────────

    private function grafikPerTahunLulus(): array
    {
        $data = DataAlumni::sudahDiisi()
            ->select('tahun_lulus', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus')
            ->get();

        return [
            'labels' => $data->pluck('tahun_lulus')->map(fn($t) => "Lulusan $t")->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Distribusi Gaji ──────────────────────────────────────────────

    private function grafikDistribusiGaji(?int $tahun): array
    {
        $urutanGaji = ['di_bawah_1jt', '1jt_2jt', '2jt_3jt', '3jt_5jt', 'di_atas_5jt'];
        $labelGaji  = ['< 1 Juta', '1–2 Juta', '2–3 Juta', '3–5 Juta', '> 5 Juta'];

        $raw = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->whereNotNull('skala_gaji_bulanan')
            ->select('skala_gaji_bulanan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('skala_gaji_bulanan')
            ->pluck('jumlah', 'skala_gaji_bulanan');

        return [
            'labels' => $labelGaji,
            'data'   => array_map(fn($k) => $raw[$k] ?? 0, $urutanGaji),
        ];
    }

    // ── Grafik: Jenis Kelamin ────────────────────────────────────────────────

    private function grafikJenisKelamin(?int $tahun): array
    {
        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->select('jenis_kelamin', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('jenis_kelamin')
            ->get();

        return [
            'labels' => $data->map(fn($d) => ucfirst($d->jenis_kelamin))->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Kesesuaian Bidang Pekerjaan ──────────────────────────────────

    private function grafikKesesuaianBidang(?int $tahun): array
    {
        $labelMap = [
            'sesuai'           => 'Sesuai',
            'tidak_sesuai'     => 'Tidak Sesuai',
            'sebagian_sesuai'  => 'Sebagian Sesuai',
        ];

        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->whereNotNull('kesesuaian_bidang')
            ->select('kesesuaian_bidang', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kesesuaian_bidang')
            ->get();

        return [
            'labels' => $data->map(fn($d) => $labelMap[$d->kesesuaian_bidang] ?? $d->kesesuaian_bidang)->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Jenjang Kuliah ───────────────────────────────────────────────

    private function grafikJenjangKuliah(?int $tahun): array
    {
        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->whereNotNull('jenjang_kuliah')
            ->select('jenjang_kuliah', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('jenjang_kuliah')
            ->orderByDesc('jumlah')
            ->get();

        return [
            'labels' => $data->pluck('jenjang_kuliah')->map(fn($j) => strtoupper($j))->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }

    // ── Grafik: Lama Tunggu ──────────────────────────────────────────────────

    private function grafikLamaTunggu(?int $tahun): array
    {
        $rentang = [
            '0'    => [0, 0],
            '1-3'  => [1, 3],
            '4-6'  => [4, 6],
            '7-12' => [7, 12],
            '>12'  => [13, 999],
        ];

        $result = [];
        foreach ($rentang as $label => [$min, $max]) {
            $result[] = DataAlumni::sudahDiisi()
                ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
                ->whereNotNull('lama_tunggu_bulan')
                ->whereBetween('lama_tunggu_bulan', [$min, $max])
                ->count();
        }

        return [
            'labels' => ['Langsung (0 bln)', '1–3 Bln', '4–6 Bln', '7–12 Bln', '> 12 Bln'],
            'data'   => $result,
        ];
    }

    // ── Grafik: Rata-rata Nilai ──────────────────────────────────────────────

    private function grafikNilaiRata(?int $tahun): array
    {
        $query = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun));

        $avg = $query->selectRaw('
            ROUND(AVG(nilai_kompetensi), 2)        as kompetensi,
            ROUND(AVG(nilai_soft_skill), 2)        as soft_skill,
            ROUND(AVG(nilai_fasilitas_sekolah), 2) as fasilitas,
            ROUND(AVG(nilai_kepuasan_sekolah), 2)  as kepuasan
        ')->first();

        return [
            'labels' => ['Kompetensi', 'Soft Skill', 'Fasilitas', 'Kepuasan'],
            'data'   => [
                (float) ($avg->kompetensi ?? 0),
                (float) ($avg->soft_skill ?? 0),
                (float) ($avg->fasilitas  ?? 0),
                (float) ($avg->kepuasan   ?? 0),
            ],
        ];
    }

    // ── Grafik: Sebaran Provinsi Kerja ───────────────────────────────────────

    private function grafikProvinsiKerja(?int $tahun): array
    {
        $data = DataAlumni::sudahDiisi()
            ->when($tahun, fn($q) => $q->where('tahun_lulus', $tahun))
            ->whereNotNull('provinsi_bekerja')
            ->select('provinsi_bekerja', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('provinsi_bekerja')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('provinsi_bekerja')->toArray(),
            'data'   => $data->pluck('jumlah')->toArray(),
        ];
    }
}
