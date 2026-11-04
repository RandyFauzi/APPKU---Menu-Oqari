<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
@props(['shop' => null, 'platform' => false])
@php
    $shop ??= auth()->user()?->shop;
    $themeColor = strtolower($shop?->primary_color ?? config('theme.primary'));
    if (! array_key_exists($themeColor, config('theme.primary_options'))) {
        $themeColor = config('theme.primary');
    }
    $themeRgb = implode(' ', sscanf($themeColor, '#%02x%02x%02x'));
@endphp
<x-shop-favicon :shop="$shop" :platform="$platform" />
<meta name="theme-color" content="{{ $themeColor }}">
<style>:root { --oq-primary-rgb: {{ $themeRgb }}; --color-primary: rgb({{ $themeRgb }}); --color-secondary: var(--oq-primary-dark); }</style>
<script>window.OQARI_THEME = { primary: {{ Illuminate\Support\Js::from($themeColor) }}, primaryOptions: {{ Illuminate\Support\Js::from(config('theme.primary_options')) }} };</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
