{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk – Tracer Study</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

<div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
    <h1 class="text-2xl font-bold text-center text-blue-700 mb-2">🎓 Tracer Study</h1>
    <p class="text-center text-gray-500 mb-6 text-sm">Masuk ke akun Anda</p>

    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.proses') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="email@contoh.com">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input type="password" name="kata_sandi" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="••••••••">
        </div>

        <div class="flex items-center mb-6">
            <input type="checkbox" name="ingat_saya" id="ingat_saya" class="mr-2">
            <label for="ingat_saya" class="text-sm text-gray-600">Ingat saya</label>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg py-2.5 text-sm transition">
            Masuk
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
        Belum punya akun?
        <a href="{{ route('daftar') }}" class="text-blue-600 hover:underline font-medium">Daftar di sini</a>
    </p>
</div>

</body>
</html>
