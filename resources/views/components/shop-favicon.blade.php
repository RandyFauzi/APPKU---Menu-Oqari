@props(['shop' => null, 'platform' => false])
@php
    $faviconShop = $platform ? null : ($shop ?? auth()->user()?->shop);
    $fallbackIcon = asset('logo-oqari.webp');
    $faviconUrl = $faviconShop?->logo_url ?: $fallbackIcon;
@endphp
<link id="favicon" rel="icon" href="{{ $faviconUrl }}" data-fallback-url="{{ $fallbackIcon }}">
