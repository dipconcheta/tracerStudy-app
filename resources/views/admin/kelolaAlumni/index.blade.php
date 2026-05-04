@extends('layouts.admin')

@section('title', 'Kelola Alumni')
@section('header_title', 'Manajemen Data Alumni')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Data Alumni</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dan verifikasi data alumni yang telah mengisi form tracer study.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.alumni.ekspor') }}" method="GET" class="inline">
                    <!-- Pass current filters to export -->
                    <input type="hidden" name="tahun_lulus" value="{{ request('tahun_lulus') }}">
                    <input type="hidden" name="jurusan" value="{{ request('jurusan') }}">
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <i class="ph ph-download-simple text-lg"></i>
                        Ekspor CSV
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('admin.alumni.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Cari -->
            <div class="lg:col-span-2">
                <label for="cari" class="sr-only">Cari Alumni</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" name="cari" id="cari" value="{{ request('cari') }}" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-shadow" placeholder="Cari nama, NIS, atau NISN...">
                </div>
            </div>

            <!-- Tahun Lulus -->
            <div>
                <label for="tahun_lulus" class="sr-only">Tahun Lulus</label>
                <select name="tahun_lulus" id="tahun_lulus" class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm appearance-none transition-shadow">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunTersedia as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun_lulus') == $tahun ? 'selected' : '' }}>Lulusan {{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jurusan -->
            <div>
                <label for="jurusan" class="sr-only">Jurusan</label>
                <select name="jurusan" id="jurusan" class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm appearance-none transition-shadow">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur }}" {{ request('jurusan') == $jur ? 'selected' : '' }}>{{ $jur }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Saat Ini -->
            <div>
                <label for="status" class="sr-only">Status</label>
                <select name="status" id="status" class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm appearance-none transition-shadow">
                    <option value="">Semua Status</option>
                    <option value="bekerja" {{ request('status') == 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                    <option value="wirausaha" {{ request('status') == 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                    <option value="kuliah" {{ request('status') == 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                    <option value="kuliah_dan_bekerja" {{ request('status') == 'kuliah_dan_bekerja' ? 'selected' : '' }}>Kuliah & Bekerja</option>
                    <option value="belum_bekerja" {{ request('status') == 'belum_bekerja' ? 'selected' : '' }}>Belum Bekerja</option>
                    <option value="tidak_melanjutkan" {{ request('status') == 'tidak_melanjutkan' ? 'selected' : '' }}>Tidak Melanjutkan</option>
                </select>
            </div>

            <!-- Verifikasi -->
            <div>
                <label for="verifikasi" class="sr-only">Verifikasi</label>
                <select name="verifikasi" id="verifikasi" class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm appearance-none transition-shadow">
                    <option value="">Semua Verifikasi</option>
                    <option value="ya" {{ request('verifikasi') == 'ya' ? 'selected' : '' }}>Sudah Verifikasi</option>
                    <option value="tidak" {{ request('verifikasi') == 'tidak' ? 'selected' : '' }}>Belum Verifikasi</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="lg:col-span-4 flex items-center justify-end gap-3">
                @if(request()->anyFilled(['cari', 'tahun_lulus', 'jurusan', 'status', 'verifikasi']))
                    <a href="{{ route('admin.alumni.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Reset
                    </a>
                @endif
                <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow transition-all flex items-center gap-2">
                    <i class="ph ph-funnel text-lg"></i>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama & Kontak</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendidikan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Tracer</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Verifikasi</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($alumniList as $alumni)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold border border-primary-200 dark:border-primary-800">
                                        {{ substr($alumni->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $alumni->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">NIS: {{ $alumni->nomor_induk_siswa }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-300">{{ $alumni->jurusan_saat_sekolah }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Lulusan {{ $alumni->tahun_lulus }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $alumni->labelStatusSaatIni() ?? ucwords(str_replace('_', ' ', $alumni->status_saat_ini)) }}
                                </span>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Diisi: {{ $alumni->diisi_pada ? $alumni->diisi_pada->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($alumni->sudah_diverifikasi)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <i class="ph-fill ph-check-circle"></i> Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-500">
                                        <i class="ph-fill ph-clock"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.alumni.tampilkan', $alumni) }}" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 bg-gray-50 hover:bg-primary-50 dark:bg-gray-700 dark:hover:bg-primary-900/30 rounded-lg transition-all" title="Detail">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>
                                
                                @if(!$alumni->sudah_diverifikasi)
                                    <form action="{{ route('admin.alumni.verifikasi', $alumni) }}" method="POST" class="inline-block ml-1">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 bg-gray-50 hover:bg-green-50 dark:bg-gray-700 dark:hover:bg-green-900/30 rounded-lg transition-all" title="Verifikasi" onclick="return confirm('Verifikasi data alumni ini?')">
                                            <i class="ph ph-check-circle text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                        <i class="ph ph-users-three text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada data alumni yang cocok dengan filter yang diberikan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($alumniList->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $alumniList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
