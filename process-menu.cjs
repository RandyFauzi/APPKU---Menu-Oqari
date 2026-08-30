const fs = require('fs');
let content = fs.readFileSync('resources/views/shop/menu.blade.php', 'utf8');

// Title
content = content.replace(/<title>.*?<\/title>/, '<title>{{ ->name }}</title>');

// Favicon
content = content.replace(/<link rel="icon" type="image\/webp" href="Pavico\.webp">/, 
@if(isset() && ->logo_url)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . ->logo_url) }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('Pavico.webp') }}">
    @endif);

// Tailwind config
content = content.replace(/primary: '#1E5A7A'/, "primary: '{{ ->primary_color ?? \'#1E5A7A\' }}'");

// Splash screen logo and text
content = content.replace(/<img src="Pavico\.webp" alt="Bitten Coffee Logo"/, <img src="{{ ->logo_url ? asset('storage/' . ->logo_url) : asset('Pavico.webp') }}" alt="{{ ->name }} Logo");
content = content.replace(/<h1 class="text-3xl font-sans font-black mt-4 text-primary tracking-tight drop-shadow-md">BITTEN COFFEE<\/h1>/, <h1 class="text-3xl font-sans font-black mt-4 text-primary tracking-tight drop-shadow-md uppercase">{{ ->name }}</h1>);
content = content.replace(/<p class="text-\[10px\] font-bold mt-2 text-textdark uppercase tracking-widest drop-shadow-md bg-white\/50 px-4 py-1\.5 rounded-full">Vintage Izakaya<\/p>/, `); // Removed Vintage Izakaya

// Header logo and text
content = content.replace(/<img src="Pavico\.webp" alt="Logo"/, <img src="{{ ->logo_url ? asset('storage/' . ->logo_url) : asset('Pavico.webp') }}" alt="Logo");
content = content.replace(/<h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight">BITTEN COFFEE<\/h1>/, <h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight uppercase">{{ ->name }}</h1>);
content = content.replace(/<p class="text-\[9px\] text-textdark font-bold uppercase tracking-widest">Vintage Izakaya<\/p>/, `);

// Table text
content = content.replace(/<p class="text-xs font-bold text-textdark">Meja 04, Bitten Coffee<\/p>/, <p class="text-xs font-bold text-textdark">Meja {{  ?? '(...)' }}, {{ ->name }}</p>);

// CSS paths
content = content.replace(/href="css\/style\.css"/, href="{{ asset('css/style.css') }}");
content = content.replace(/url\('Assest\/Loading Screen\.webp'\)/, url('{{ asset('Assest/Loading Screen.webp') }}'));

fs.writeFileSync('resources/views/shop/menu.blade.php', content);
