@extends('layouts.admin')

@section('title', 'Detail Alumni - ' . $alumni->nama_lengkap)
@section('header_title', 'Detail Data Alumni')

@section('content')
<div class="space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.alumni.index') }}" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    {{ $alumni->nama_lengkap }}
                    @if($alumni->sudah_diverifikasi)
                        <i class="ph-fill ph-check-circle text-green-500" title="Terverifikasi"></i>
                    @else
                        <i class="ph-fill ph-clock text-yellow-500" title="Menunggu Verifikasi"></i>
                    @endif
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">NIS: {{ $alumni->nomor_induk_siswa }} • NISN: {{ $alumni->nisn }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if(!$alumni->sudah_diverifikasi)
                <form action="{{ route('admin.alumni.verifikasi', $alumni) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow transition-all flex items-center gap-2" onclick="return confirm('Verifikasi data alumni ini?')">
                        <i class="ph ph-check-circle text-lg"></i>
                        Verifikasi Data
                    </button>
                </form>
            @else
                <form action="{{ route('admin.alumni.batal.verifikasi', $alumni) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 dark:text-red-400 rounded-xl text-sm font-medium border border-red-200 dark:border-red-800 transition-all flex items-center gap-2" onclick="return confirm('Batalkan verifikasi data ini?')">
                        <i class="ph ph-x-circle text-lg"></i>
                        Batal Verifikasi
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Info Dasar & Status -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Profil Singkat -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center text-center pb-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/40 dark:to-primary-800/40 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-3xl border-4 border-white dark:border-gray-800 shadow-md mb-4">
                        {{ substr($alumni->nama_lengkap, 0, 1) }}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $alumni->nama_lengkap }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ ucwords($alumni->jenis_kelamin) }}</p>
                </div>
                
                <div class="pt-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <i class="ph ph-map-pin text-gray-400 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->tempat_lahir }}, {{ $alumni->tanggal_lahir?->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="ph ph-house text-gray-400 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alamat Lengkap</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->alamat_lengkap ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="ph ph-phone text-gray-400 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nomor Telepon</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->nomor_telepon ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="ph ph-envelope-simple text-gray-400 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email Pengguna</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->User->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Tracer Study -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Data Pengisian</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status Saat Ini</p>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $alumni->labelStatusSaatIni() ?? ucwords(str_replace('_', ' ', $alumni->status_saat_ini)) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Diisi Pada</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->diisi_pada ? $alumni->diisi_pada->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Diupdate Pada</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->updated_at ? $alumni->updated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>

                    @if($alumni->sudah_diverifikasi)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 bg-green-50/50 dark:bg-green-900/10 p-3 rounded-xl">
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium mb-1">Diverifikasi Oleh</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $alumni->verifikator->nama_lengkap ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $alumni->diverifikasi_pada ? $alumni->diverifikasi_pada->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Detail Pendidikan, Pekerjaan, dll -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Riwayat Pendidikan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-100 dark:border-gray-700">
                    <i class="ph ph-graduation-cap text-primary-500 text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Pendidikan Sekolah</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jurusan</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $alumni->jurusan_saat_sekolah ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tahun Masuk</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $alumni->tahun_masuk ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tahun Lulus</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $alumni->tahun_lulus ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Detail Pekerjaan / Kuliah / Wirausaha -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-100 dark:border-gray-700">
                    <i class="ph ph-briefcase text-primary-500 text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aktivitas Saat Ini</h3>
                </div>

                @if(in_array($alumni->status_saat_ini, ['bekerja', 'kuliah_dan_bekerja']))
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">Detail Pekerjaan</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Perusahaan / Tempat Kerja</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->nama_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jabatan / Posisi</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->jabatan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Provinsi Tempat Kerja</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->provinsi_bekerja ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Skala Gaji Bulanan</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $alumni->skala_gaji_bulanan)) ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($alumni->status_saat_ini, ['kuliah', 'kuliah_dan_bekerja']))
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">Detail Perkuliahan</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Perguruan Tinggi</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->nama_perguruan_tinggi ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Program Studi</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->program_studi ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jenjang Kuliah</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($alumni->jenjang_kuliah) ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sumber Dana Kuliah</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $alumni->sumber_dana_kuliah)) ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($alumni->status_saat_ini === 'wirausaha')
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">Detail Wirausaha</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-green-50/50 dark:bg-green-900/10 p-4 rounded-xl border border-green-100 dark:border-green-900/30">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Usaha</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->nama_usaha ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bidang Usaha</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alumni->bidang_usaha ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Skala Pendapatan Bulanan</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $alumni->skala_gaji_bulanan)) ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($alumni->status_saat_ini === 'belum_bekerja' || $alumni->status_saat_ini === 'tidak_melanjutkan')
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada detail aktivitas tambahan untuk status saat ini.</p>
                    </div>
                @endif

            </div>

            <!-- Analisis Tracer Study -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-100 dark:border-gray-700">
                    <i class="ph ph-chart-bar text-primary-500 text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Analisis & Kepuasan</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8">
                    @if(in_array($alumni->status_saat_ini, ['bekerja', 'kuliah_dan_bekerja', 'wirausaha']))
                    <div>
                        <h4 class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider font-semibold">Kesesuaian Bidang</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full {{ $alumni->kesesuaian_bidang == 'sesuai' ? 'bg-green-100 text-green-600 dark:bg-green-900/30' : ($alumni->kesesuaian_bidang == 'sebagian_sesuai' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30' : 'bg-red-100 text-red-600 dark:bg-red-900/30') }} flex items-center justify-center font-bold">
                                <i class="ph {{ $alumni->kesesuaian_bidang == 'sesuai' ? 'ph-check' : ($alumni->kesesuaian_bidang == 'sebagian_sesuai' ? 'ph-minus' : 'ph-x') }} text-xl"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $alumni->kesesuaian_bidang)) ?? '-' }}</span>
                        </div>
                    </div>
                    @endif

                    <div>
                        <h4 class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider font-semibold">Lama Tunggu Pekerjaan Pertama</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 flex items-center justify-center font-bold">
                                <i class="ph ph-hourglass-high text-xl"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $alumni->lama_tunggu_bulan !== null ? $alumni->lama_tunggu_bulan . ' Bulan' : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <h4 class="text-xs text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider font-semibold">Penilaian Terhadap Sekolah (1-5)</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                    
                    <!-- Kompetensi -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Kompetensi Jurusan</p>
                        <div class="flex justify-center text-yellow-400 text-lg mb-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $alumni->nilai_kompetensi ? 'ph-fill' : 'ph' }} ph-star"></i>
                            @endfor
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alumni->nilai_kompetensi ?? '-' }}/5</p>
                    </div>

                    <!-- Soft Skill -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Soft Skill</p>
                        <div class="flex justify-center text-yellow-400 text-lg mb-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $alumni->nilai_soft_skill ? 'ph-fill' : 'ph' }} ph-star"></i>
                            @endfor
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alumni->nilai_soft_skill ?? '-' }}/5</p>
                    </div>

                    <!-- Fasilitas -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Fasilitas</p>
                        <div class="flex justify-center text-yellow-400 text-lg mb-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $alumni->nilai_fasilitas_sekolah ? 'ph-fill' : 'ph' }} ph-star"></i>
                            @endfor
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $alumni->nilai_fasilitas_sekolah ?? '-' }}/5</p>
                    </div>

                    <!-- Kepuasan Keseluruhan -->
                    <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-xl border border-primary-100 dark:border-primary-800/30 text-center">
                        <p class="text-xs text-primary-700 dark:text-primary-400 mb-2 font-medium">Kepuasan Umum</p>
                        <div class="flex justify-center text-yellow-400 text-lg mb-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $alumni->nilai_kepuasan_sekolah ? 'ph-fill' : 'ph' }} ph-star"></i>
                            @endfor
                        </div>
                        <p class="text-lg font-bold text-primary-700 dark:text-primary-400">{{ $alumni->nilai_kepuasan_sekolah ?? '-' }}/5</p>
                    </div>

                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-5 rounded-xl border border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider font-semibold">Saran & Masukan Untuk Sekolah</h4>
                    <p class="text-sm text-gray-800 dark:text-gray-200 italic leading-relaxed">
                        "{{ $alumni->saran_masukan ?? 'Tidak ada saran yang diberikan.' }}"
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
