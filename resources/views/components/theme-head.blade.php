<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
@props(['shop' => null, 'platform' => false])
@php
    $shop ??= auth()->user()?->shop;
    
    // Validasi warna hex kustom (misal: #AF4928 atau #af4928) atau fallback ke default
    $rawColor = trim($shop?->primary_color ?? '');
    if (preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $rawColor)) {
        if (strlen($rawColor) === 4) {
            $themeColor = '#' . $rawColor[1] . $rawColor[1] . $rawColor[2] . $rawColor[2] . $rawColor[3] . $rawColor[3];
        } else {
            $themeColor = $rawColor;
        }
    } else {
        $themeColor = config('theme.primary', '#1e5a7a');
    }
    
    $themeColor = strtolower($themeColor);
    $rgbValues = sscanf($themeColor, '#%02x%02x%02x') ?: [30, 90, 122];
    $themeRgb = implode(' ', $rgbValues);
    
    $darkRgb = implode(' ', [
        max(0, (int) round($rgbValues[0] * 0.8)),
        max(0, (int) round($rgbValues[1] * 0.8)),
        max(0, (int) round($rgbValues[2] * 0.8)),
    ]);
@endphp
<x-shop-favicon :shop="$shop" :platform="$platform" />
<meta name="theme-color" content="{{ $themeColor }}">
<style>
:root {
    --oq-primary-rgb: {{ $themeRgb }};
    --oq-primary: rgb({{ $themeRgb }});
    --oq-primary-dark: rgb({{ $darkRgb }});
    --color-primary: rgb({{ $themeRgb }});
    --color-secondary: rgb({{ $darkRgb }});
}
</style>
<script>
window.OQARI_THEME = { 
    primary: {{ Illuminate\Support\Js::from($themeColor) }}, 
    primaryOptions: {{ Illuminate\Support\Js::from(config('theme.primary_options')) }} 
};
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
