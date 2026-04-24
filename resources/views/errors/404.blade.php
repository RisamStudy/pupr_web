<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.11.5/dist/gsap.min.js"></script>
</head>
<body class="antialiased bg-gradient-to-br from-blue-100 to-indigo-200 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-6 py-8 bg-white shadow-2xl rounded-lg text-center">
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-gray-800 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-8">Oops! Halaman tidak ditemukan</p>
        <p class="text-gray-500 mb-8">Maaf, halaman yang Anda cari sepertinya telah berpetualang ke tempat lain.</p>
        <a href="{{ url('/') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full shadow-lg hover:bg-indigo-700 transition duration-300 ease-in-out hover:-translate-y-1 hover:scale-105">
            Kembali ke Beranda
        </a>
    </div>

    <script>
        gsap.from("svg", {duration: 1, y: -50, opacity: 0, ease: "bounce"});
        gsap.from("h1", {duration: 1, scale: 0.5, opacity: 0, delay: 0.5, ease: "back"});
        gsap.from("p", {duration: 1, y: 20, opacity: 0, delay: 0.8, stagger: 0.2});
        gsap.from("a", {duration: 1, delay: 1.2, ease: "power2.out"});
    </script>
</body>
</html>
