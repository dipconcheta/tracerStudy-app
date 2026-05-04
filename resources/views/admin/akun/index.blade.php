@extends('layouts.admin')

@section('title', 'Manajemen Akun')
@section('header_title', 'Manajemen Akun Alumni')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    <!-- Header Actions & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Akun Alumni</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola akses login untuk para alumni.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.akun.index') }}" method="GET" class="flex items-center w-full md:w-auto">
                    <label for="cari" class="sr-only">Cari Akun</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-magnifying-glass text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" name="cari" id="cari" value="{{ request('cari') }}" class="block w-full md:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-shadow" placeholder="Cari nama atau email...">
                    </div>
                    @if(request('cari'))
                        <a href="{{ route('admin.akun.index') }}" class="ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                            <i class="ph ph-x-circle text-xl"></i>
                        </a>
                    @endif
                </form>

                <button @click="showModal = true" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow transition-all flex items-center gap-2 flex-shrink-0">
                    <i class="ph ph-plus-circle text-lg"></i>
                    <span class="hidden sm:inline">Buat Akun</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengguna</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Tracer</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akses Login</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($akunList as $akun)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold border border-primary-200 dark:border-primary-800">
                                        {{ substr($akun->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $akun->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Terdaftar: {{ $akun->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-300">{{ $akun->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($akun->dataAlumni)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Sudah Mengisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Belum Mengisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($akun->aktif)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.akun.toggle', $akun) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($akun->aktif)
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 bg-gray-50 hover:bg-red-50 dark:bg-gray-700 dark:hover:bg-red-900/30 rounded-lg transition-all" title="Nonaktifkan" onclick="return confirm('Nonaktifkan akses login untuk akun ini?')">
                                            <i class="ph ph-lock-key text-lg"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 bg-gray-50 hover:bg-green-50 dark:bg-gray-700 dark:hover:bg-green-900/30 rounded-lg transition-all" title="Aktifkan" onclick="return confirm('Aktifkan akses login untuk akun ini?')">
                                            <i class="ph ph-lock-key-open text-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                        <i class="ph ph-users text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada akun alumni yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($akunList->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $akunList->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Buat Akun -->
    <div x-cloak x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     @click.away="showModal = false"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                    
                    <div class="px-6 py-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Buat Akun Alumni Baru</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan akun login baru secara manual untuk alumni.</p>
                    </div>

                    <form action="{{ route('admin.akun.buat') }}" method="POST">
                        @csrf
                        <div class="px-6 py-6 space-y-5">
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" required class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2.5 px-3 border transition-colors" placeholder="Masukkan nama lengkap">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email</label>
                                <input type="email" name="email" id="email" required class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2.5 px-3 border transition-colors" placeholder="email@example.com">
                            </div>

                            <div>
                                <label for="kata_sandi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi</label>
                                <input type="password" name="kata_sandi" id="kata_sandi" required minlength="8" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2.5 px-3 border transition-colors" placeholder="Minimal 8 karakter">
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 inline-flex justify-center rounded-xl border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
