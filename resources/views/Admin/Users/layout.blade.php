<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>User Management — OQARI</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-theme-head :shop="auth()->user()->shop" />
</head><body class="min-h-screen bg-ivory text-ink font-sans">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content bg-ivory">
<div class="oq-frieze" aria-hidden="true"></div>
<main class="max-w-7xl mx-auto p-4 md:p-8">
@if(session('success'))<p role="status" class="p-4 mb-5 rounded-xl bg-green-50 text-green-800 border border-green-200">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert" class="p-4 mb-5 rounded-xl bg-red-50 text-red-800 border border-red-200"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main></div></div>
</body></html>
