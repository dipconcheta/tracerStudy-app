{{-- resources/views/admin/dashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – Tracer Study</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navbar --}}
<nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center shadow">
    <span class="font-bold text-lg">🎓 Tracer Study – Admin</span>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('admin.alumni.index') }}" class="hover:underline">Data Alumni</a>
        <a href="{{ route('admin.akun.index') }}" class="hover:underline">Kelola Akun</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button class="hover:underline">Keluar</button>
        </form>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Visualisasi</h1>

        {{-- Filter Tahun Lulus --}}
        <form method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Filter Tahun Lulus:</label>
            <select name="tahun_lulus" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Tahun</option>
                @foreach($grafik['tahun_tersedia'] as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun_lulus') == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @php
            $kartu = [
                ['label' => 'Total Alumni',       'nilai' => $statistik['total_alumni'],      'warna' => 'bg-blue-500'],
                ['label' => 'Sudah Mengisi',       'nilai' => $statistik['sudah_mengisi'],      'warna' => 'bg-green-500'],
                ['label' => 'Belum Mengisi',       'nilai' => $statistik['belum_mengisi'],      'warna' => 'bg-yellow-500'],
                ['label' => 'Belum Verifikasi',    'nilai' => $statistik['belum_verifikasi'],   'warna' => 'bg-red-500'],
                ['label' => '% Respons',           'nilai' => $statistik['persentase_isi'].'%', 'warna' => 'bg-purple-500'],
                ['label' => 'Rata Kepuasan',       'nilai' => $statistik['rata_kepuasan'].'/5', 'warna' => 'bg-indigo-500'],
            ];
        @endphp
        @foreach($kartu as $k)
            <div class="{{ $k['warna'] }} text-white rounded-xl p-4 text-center shadow">
                <div class="text-2xl font-bold">{{ $k['nilai'] }}</div>
                <div class="text-xs mt-1 opacity-90">{{ $k['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Baris Grafik 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Grafik Status Saat Ini (Donut) --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Status Alumni Saat Ini</h3>
            <div class="relative h-64">
                <canvas id="grafikStatus"></canvas>
            </div>
        </div>

        {{-- Grafik Jurusan (Bar) --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Sebaran per Jurusan</h3>
            <div class="relative h-64">
                <canvas id="grafikJurusan"></canvas>
            </div>
        </div>
    </div>

    {{-- Baris Grafik 2 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

        {{-- Grafik Gaji --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Distribusi Gaji Bulanan</h3>
            <div class="relative h-56">
                <canvas id="grafikGaji"></canvas>
            </div>
        </div>

        {{-- Grafik Kesesuaian Bidang --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Kesesuaian Bidang Kerja</h3>
            <div class="relative h-56">
                <canvas id="grafikKesesuaian"></canvas>
            </div>
        </div>

        {{-- Grafik Jenis Kelamin --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Jenis Kelamin</h3>
            <div class="relative h-56">
                <canvas id="grafikGender"></canvas>
            </div>
        </div>
    </div>

    {{-- Baris Grafik 3 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Grafik Tahun Lulus (Line) --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Responden per Tahun Lulus</h3>
            <div class="relative h-64">
                <canvas id="grafikTahun"></canvas>
            </div>
        </div>

        {{-- Grafik Lama Tunggu --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Lama Tunggu Kerja/Kuliah</h3>
            <div class="relative h-64">
                <canvas id="grafikTunggu"></canvas>
            </div>
        </div>
    </div>

    {{-- Grafik Radar Nilai --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Rata-rata Penilaian Alumni (skala 1–5)</h3>
            <div class="relative h-64">
                <canvas id="grafikNilai"></canvas>
            </div>
        </div>

        {{-- Grafik Provinsi Kerja --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Top 10 Provinsi Tempat Kerja</h3>
            <div class="relative h-64">
                <canvas id="grafikProvinsi"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- Data JSON untuk Chart.js --}}
<script>
const grafik = @json($grafik);

const warnaPalette = [
    '#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6',
    '#EC4899','#06B6D4','#84CC16','#F97316','#6366F1'
];

function buatGrafik(id, tipe, labels, data, opsi = {}) {
    const ctx = document.getElementById(id).getContext('2d');
    return new Chart(ctx, {
        type: tipe,
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: tipe === 'line' ? 'rgba(59,130,246,0.15)' : warnaPalette,
                borderColor: tipe === 'line' || tipe === 'radar' ? '#3B82F6' : warnaPalette,
                borderWidth: tipe === 'bar' ? 0 : 2,
                fill: tipe === 'line',
                tension: 0.4,
                pointBackgroundColor: '#3B82F6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: tipe === 'doughnut' || tipe === 'pie' || tipe === 'radar' } },
            ...opsi
        }
    });
}

// Inisialisasi semua grafik
buatGrafik('grafikStatus',    'doughnut', grafik.status_saat_ini.labels,   grafik.status_saat_ini.data);
buatGrafik('grafikJurusan',   'bar',      grafik.jurusan.labels,            grafik.jurusan.data,
    { indexAxis: 'y', plugins: { legend: { display: false } } });
buatGrafik('grafikGaji',      'bar',      grafik.gaji.labels,               grafik.gaji.data,
    { plugins: { legend: { display: false } } });
buatGrafik('grafikKesesuaian','pie',      grafik.kesesuaian_bidang.labels,  grafik.kesesuaian_bidang.data);
buatGrafik('grafikGender',    'doughnut', grafik.jenis_kelamin.labels,      grafik.jenis_kelamin.data);
buatGrafik('grafikTahun',     'line',     grafik.tahun_lulus.labels,        grafik.tahun_lulus.data);
buatGrafik('grafikTunggu',    'bar',      grafik.lama_tunggu.labels,        grafik.lama_tunggu.data,
    { plugins: { legend: { display: false } } });
buatGrafik('grafikNilai',     'radar',    grafik.nilai_rata.labels,         grafik.nilai_rata.data,
    { scales: { r: { min: 0, max: 5 } } });
buatGrafik('grafikProvinsi',  'bar',      grafik.provinsi_kerja.labels,     grafik.provinsi_kerja.data,
    { indexAxis: 'y', plugins: { legend: { display: false } } });
</script>

</body>
</html>
