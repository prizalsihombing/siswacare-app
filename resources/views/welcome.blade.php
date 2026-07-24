<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISWACARE - Sistem Informasi Siswa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-gray-800 font-sans antialiased flex flex-col justify-between p-4 lg:p-8 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/bg-sekolah.png') }}');">

    <!-- Container Utama (Split Screen Layout) -->
    <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-center my-auto">
        
        <!-- BAGIAN KIRI: Informasi & Fitur -->
        <div class="lg:col-span-7 text-gray-900 space-y-6">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 text-white p-2.5 rounded-xl shadow-md font-bold text-xl">🛡️</div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-wide text-blue-900">SISWACARE</h1>
                    <p class="text-xs text-blue-700 font-medium">Sistem Informasi Siswa</p>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-4xl lg:text-5xl font-black leading-tight text-gray-900">
                    Kelola Data Siswa <br>Lebih Mudah, <br><span class="text-blue-600">Sekolah Lebih Maju</span>
                </h2>
                <p class="text-gray-700 text-sm lg:text-base max-w-lg leading-relaxed pt-2">
                    SISWACARE adalah sistem informasi sekolah yang membantu guru dan staf kesiswaan dalam mengelola data siswa secara terintegrasi, aman, dan efisien.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div class="bg-white/80 backdrop-blur-md p-3.5 rounded-2xl border border-white shadow-sm">
                    <div class="text-xl mb-1">👥</div>
                    <h4 class="font-bold text-xs text-gray-900">Data Terintegrasi</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5">Satu sistem terpusat</p>
                </div>
                <div class="bg-white/80 backdrop-blur-md p-3.5 rounded-2xl border border-white shadow-sm">
                    <div class="text-xl mb-1">🛡️</div>
                    <h4 class="font-bold text-xs text-gray-900">Aman & Terpercaya</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5">Keamanan terjamin</p>
                </div>
                <div class="bg-white/80 backdrop-blur-md p-3.5 rounded-2xl border border-white shadow-sm">
                    <div class="text-xl mb-1">📈</div>
                    <h4 class="font-bold text-xs text-gray-900">Laporan Lengkap</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5">Cepat dan akurat</p>
                </div>
                <div class="bg-white/80 backdrop-blur-md p-3.5 rounded-2xl border border-white shadow-sm">
                    <div class="text-xl mb-1">🔔</div>
                    <h4 class="font-bold text-xs text-gray-900">Real-time</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5">Informasi instan</p>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: Card Form Login -->
        <div class="lg:col-span-5 bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl p-8 lg:p-10 border border-white">
            <div class="text-center space-y-2 mb-6">
                <div class="inline-flex justify-center items-center w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl mb-1 shadow-inner text-3xl">🎓</div>
                <h3 class="text-2xl font-bold text-gray-900">Selamat Datang <span class="text-blue-600">Kembali!</span></h3>
                <p class="text-sm text-gray-500">Silakan login untuk melanjutkan</p>
            </div>

            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-xl text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">👤</span>
                        <input type="text" name="username" :value="old('username')" required autofocus autocomplete="username" 
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
                            placeholder="Masukkan email / nomor induk">
                    </div>
                    @error('username')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">🔒</span>
                        <input type="password" name="password" required autocomplete="current-password" 
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mr-2">
                        Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-semibold">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition duration-200 text-sm flex items-center justify-center space-x-2">
                    <span>Login</span>
                    <span>➔</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center text-xs text-gray-400 space-y-1">
                <p class="font-semibold text-gray-500">SISWACARE</p>
                <p>Sistem Informasi Siswa &copy; 2026. All rights reserved.</p>
            </div>
        </div>

    </div>

</body>
</html>