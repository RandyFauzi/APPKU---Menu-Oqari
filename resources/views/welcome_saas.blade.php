<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Coffee Shop (SaaS Demo)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-black text-center mb-8 text-gray-800">Menu App SaaS Demo</h1>
        <div class="space-y-4">
            <a href="/bitten/menu" class="block w-full py-4 text-center rounded-xl font-bold text-white bg-[#1E5A7A] hover:opacity-90 transition">Bitten Coffee (Blue)</a>
            <a href="/goodwill/menu" class="block w-full py-4 text-center rounded-xl font-bold text-white bg-[#276749] hover:opacity-90 transition">Goodwill Coffee (Green)</a>
            <a href="/mada/menu" class="block w-full py-4 text-center rounded-xl font-bold text-white bg-[#744210] hover:opacity-90 transition">Mada Coffee (Brown)</a>
        </div>
        <p class="text-center text-xs text-gray-400 mt-8">Setiap halaman menarik warna dan menu dari database berdasarkan slug di URL.</p>
    </div>
</body>
</html>
