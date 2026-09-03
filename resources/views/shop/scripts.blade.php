    <script src="{{ asset('js/data.js') }}"></script>
    <script>
        window.SHOP_CATEGORIES = @json($categories ?? []);
        window.SHOP_DATA = @json($menuItems ?? []);
        window.SHOP_SLUG = '{{ $shop->slug }}';
        window.SHOP_HOME_URL = '{{ route("shop.menu", $shop->slug) }}';
        window.SHOP_CART_URL = '{{ route("shop.cart", $shop->slug) }}';
        window.SHOP_TRACKING_URL = '{{ route("shop.tracking", $shop->slug) }}';

        if (typeof apiData !== 'undefined') {
            const iconMap = {
                'Coffee': 'fa-coffee',
                'Tea': 'fa-leaf',
                'Foods': 'fa-utensils',
                'Snacks': 'fa-cookie',
                'Sweets': 'fa-ice-cream',
                'Beverages': 'fa-glass-water'
            };
            
            // Build categories from actual DB categories
            apiData.categories = [{ id: 'all', name: 'All Menu', icon: 'fa-star' }];
            if (window.SHOP_CATEGORIES && window.SHOP_CATEGORIES.length > 0) {
                window.SHOP_CATEGORIES.forEach(cat => {
                    apiData.categories.push({
                        id: cat.id,
                        name: cat.name,
                        icon: iconMap[cat.name] || 'fa-utensils'
                    });
                });
            }
            
            // Override highlights with shop banners
            const shopBanners = @json($shop->banners ?? []);
            if (shopBanners && shopBanners.length > 0) {
                apiData.highlights = shopBanners.map((banner, index) => ({
                    id: 'h' + (index + 1),
                    img: banner, // Use the full URL provided by Shop->banners accessor
                    title: '',
                    desc: ''
                })).filter(b => b.img); // Just filter out nulls/empty strings
                
                // Jika tidak ada banner valid yang diupload, kembalikan ke default agar tidak kosong
                if (apiData.highlights.length === 0) {
                    apiData.highlights = [];
                }
            } else {
                apiData.highlights = [];
            }
        }

        const DB = {
            get: function(tableName) {
                if (tableName === 'bitten_menu') {
                    return window.SHOP_DATA.map(item => ({
                        id: item.id,
                        name: item.name,
                        price: Number(item.price),
                        desc: item.description,
                        categoryId: item.category ? item.category.id : null,
                        categoryName: item.category ? item.category.name : '',
                        img: item.image_url ? item.image_url : '/Assests/null image.webp',
                        soldOut: item.is_sold_out
                    }));
                }
                return localStorage.getItem(tableName) ? JSON.parse(localStorage.getItem(tableName)) : [];
            },
            createOrder: function(table, name, email, phone, paymentMethod, items, total) {
                let orders = this.get('bitten_orders') || [];
                const newOrder = {
                    id: 'ORD-' + Date.now(),
                    table: table,
                    customer: name,
                    email: email,
                    phone: phone,
                    payment_method: paymentMethod,
                    items: items,
                    total: total,
                    status: 'process',
                    timestamp: new Date().toISOString()
                };
                orders.push(newOrder);
                localStorage.setItem('bitten_orders', JSON.stringify(orders));

                return fetch('/' + window.SHOP_SLUG + '/order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        table_id: table,
                        customer_name: name,
                        customer_email: email,
                        customer_phone: phone,
                        payment_method: paymentMethod,
                        items: items.map(item => ({
                            id: item.id,
                            qty: item.qty,
                            notes: item.notes
                        }))
                    })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Server error');
                    }
                    if (data.success && data.order) {
                        localStorage.setItem('gw_last_order', JSON.stringify(data.order));
                    }
                    return data;
                })
                .catch(err => {
                    console.error(err);
                    throw err;
                });
            }
        };
    </script>
    <script src="{{ asset('js/store.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
