<?php
\ = file_get_contents('resources/views/shop/menu.blade.php');
\ = preg_replace('/<title>.*?<\/title>/', '<title>{{ \->name }}</title>', \);
\ = preg_replace('/<link rel="icon" type="image\/webp" href="Pavico\.webp">/', 
    "@if(isset(\\) && \\->logo_url)\n        <link rel=\"icon\" type=\"image/png\" href=\"{{ asset('storage/' . \\->logo_url) }}\">\n    @else\n        <link rel=\"icon\" type=\"image/webp\" href=\"{{ asset('Pavico.webp') }}\">\n    @endif", \);
\ = preg_replace('/primary: \'#1E5A7A\'/', "primary: '{{ \->primary_color ?? \'#1E5A7A\' }}'", \);
\ = preg_replace('/<img src="Pavico\.webp" alt="Bitten Coffee Logo"/', '<img src="{{ \->logo_url ? asset(\'storage/\' . \->logo_url) : asset(\'Pavico.webp\') }}" alt="{{ \->name }} Logo"', \);
\ = preg_replace('/<h1 class="text-3xl font-sans font-black mt-4 text-primary tracking-tight drop-shadow-md">BITTEN COFFEE<\/h1>/', '<h1 class="text-3xl font-sans font-black mt-4 text-primary tracking-tight drop-shadow-md uppercase">{{ \->name }}</h1>', \);
\ = preg_replace('/<p class="text-\[10px\] font-bold mt-2 text-textdark uppercase tracking-widest drop-shadow-md bg-white\/50 px-4 py-1\.5 rounded-full">Vintage Izakaya<\/p>/', '', \);
\ = preg_replace('/<img src="Pavico\.webp" alt="Logo"/', '<img src="{{ \->logo_url ? asset(\'storage/\' . \->logo_url) : asset(\'Pavico.webp\') }}" alt="Logo"', \);
\ = preg_replace('/<h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight">BITTEN COFFEE<\/h1>/', '<h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight uppercase">{{ \->name }}</h1>', \);
\ = preg_replace('/<p class="text-\[9px\] text-textdark font-bold uppercase tracking-widest">Vintage Izakaya<\/p>/', '', \);
\ = preg_replace('/<p class="text-xs font-bold text-textdark">Meja 04, Bitten Coffee<\/p>/', '<p class="text-xs font-bold text-textdark">Meja {{ \ ?? \'(...)\' }}, {{ \->name }}</p>', \);
\ = preg_replace('/href="css\/style\.css"/', 'href="{{ asset(\'css/style.css\') }}"', \);
\ = preg_replace('/url\(\'Assest\/Loading Screen\.webp\'\)/', 'url(\'{{ asset(\\\'Assest/Loading Screen.webp\\\') }}\')', \);
file_put_contents('resources/views/shop/menu.blade.php', \);
