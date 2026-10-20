<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>User Management — OQARI</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-theme-head :shop="auth()->user()->shop" />
</head><body class="min-h-screen bg-cream text-dark font-sans">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content">
<div class="oq-frieze" aria-hidden="true"></div>
<header class="bg-primary text-white border-b border-gray-200"><div class="max-w-6xl mx-auto p-5 flex flex-wrap gap-4 justify-between items-center"><div><x-hellenic-emblem class="inline-block h-6 w-6 text-white" /> <strong>{{ auth()->user()->shop->name }}</strong></div><nav class="flex gap-5 items-center text-sm" aria-label="Navigasi user"><a href="{{ route(auth()->user()->homeRoute()) }}">Aplikasi</a><a href="{{ route('profile.edit') }}">Profil Saya</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form></nav></div></header>
<main class="max-w-6xl mx-auto p-4 md:p-8">
@if(session('success'))<p role="status" class="p-4 mb-5 rounded-xl bg-green-50 text-green-800 border border-green-200">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert" class="p-4 mb-5 rounded-xl bg-red-50 text-red-800 border border-red-200"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main></div></div>
</body></html>
