<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | PPDB SMK Cyber Media</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @else
        <style>/*! tailwindcss v4 */</style>
        <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    @endif
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center px-6">
        <p class="text-6xl font-bold text-blue-500">404</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-800">Halaman Tidak Ditemukan</h1>
        <p class="mt-2 text-gray-500">Halaman yang Anda cari tidak ada atau sudah dipindahkan.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block px-5 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
