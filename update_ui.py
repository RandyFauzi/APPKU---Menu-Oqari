import sys
import re

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Add AOS css
    content = content.replace('<!-- Alpine.js & Tailwind -->', '<!-- Alpine.js, Tailwind & AOS -->\n    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">\n    <!-- Alpine.js & Tailwind -->')

    # Add AOS JS
    body_close = '</body>'
    aos_init = '''
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
'''
    content = content.replace(body_close, aos_init)

    # Colors
    text = content
    text = text.replace('bg-stone-900', 'bg-white border-orange-200 text-orange-950')
    text = text.replace('bg-stone-950', 'bg-orange-50')
    text = text.replace('bg-stone-800', 'bg-orange-100')
    text = text.replace('text-stone-200', 'text-orange-800')
    text = text.replace('text-stone-300', 'text-orange-700')
    text = text.replace('text-stone-400', 'text-orange-600')
    text = text.replace('text-stone-500', 'text-gray-500')
    text = text.replace('text-stone-600', 'text-gray-600')
    text = text.replace('text-stone-700', 'text-gray-700')
    text = text.replace('text-stone-800', 'text-gray-800')
    text = text.replace('text-stone-900', 'text-orange-950')
    text = text.replace('border-stone-800', 'border-orange-200')
    text = text.replace('border-stone-200', 'border-orange-100')
    text = text.replace('amber', 'orange')
    text = text.replace('emerald', 'green')
    
    # Adding AOS to sections
    text = text.replace('<section ', '<section data-aos="fade-up" ')
    
    # Simplify Copywriting
    text = text.replace('Kelola Coffee Shop <br class="hidden sm:inline">\n                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-700 via-orange-800 to-orange-950">Lebih Cepat, Rapi,</span> <br>\n                        dan Serba Otomatis.', 
                        'POS & Smart Menu <br class="hidden sm:inline"><span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-700">Coffee Shop</span>')
    text = text.replace('Hilangkan antrean panjang di kasir dengan <strong>QR E-Menu Meja</strong> tanpa install aplikasi. Pesanan otomatis terkirim ke <strong>Kitchen Display Barista</strong>, lengkap dengan kalkulasi HPP resep kopi per gramasi secara akurat.',
                        'QR Menu, POS, & Kitchen Display dalam satu platform. Lebih cepat, hemat, dan efisien.')

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(text)

process_file(r'c:\laragon\www\Menu App\coffee-saas\resources\views\landing.blade.php')
process_file(r'c:\laragon\www\Menu App\coffee-saas\resources\views\harga.blade.php')

print("Update completed.")
