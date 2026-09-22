    <script>
        window.SHOP_CATEGORIES = @json($categories ?? []);
        window.SHOP_DATA = @json($menuItems ?? []);
        window.SHOP_SLUG = '{{ $shop->slug }}';
        window.SHOP_HOME_URL = '{{ route("shop.menu", $shop->slug) }}';
        window.SHOP_CART_URL = '{{ route("shop.cart", $shop->slug) }}';
        window.SHOP_TRACKING_URL = '{{ route("shop.tracking", $shop->slug) }}';

        // Initialize apiData to replace legacy data.js
        window.apiData = {
            categories: [],
            highlights: [],
            menu: []
        };

        const iconMap = {
            'coffee': 'fa-coffee',
            'tea': 'fa-leaf',
            'foods': 'fa-utensils',
            'food': 'fa-utensils',
            'snacks': 'fa-cookie',
            'snack': 'fa-cookie',
            'sweets': 'fa-ice-cream',
            'sweet': 'fa-ice-cream',
            'dessert': 'fa-ice-cream',
            'beverages': 'fa-cocktail',
            'beverage': 'fa-cocktail',
            'drinks': 'fa-cocktail',
            'drink': 'fa-cocktail'
        };
        
        const getIcon = (name) => {
            if (!name) return 'fa-utensils';
            const lower = name.toLowerCase().trim();
            return iconMap[lower] || 'fa-utensils';
        };
        
        window.apiData.categories = [{ id: 'all', name: 'All Menu', icon: 'fa-star' }];
        const existingCatIds = new Set(['all']);
        const existingCatNames = new Set(['all menu']);

        if (window.SHOP_CATEGORIES && window.SHOP_CATEGORIES.length > 0) {
            window.SHOP_CATEGORIES.forEach(cat => {
                window.apiData.categories.push({
                    id: cat.id,
                    name: cat.name,
                    icon: getIcon(cat.name)
                });
                existingCatIds.add(String(cat.id));
                existingCatNames.add(cat.name.toLowerCase());
            });
        }
        
        // Always extract any missing categories from products (in case a product has a category not in SHOP_CATEGORIES)
        if (window.SHOP_DATA && window.SHOP_DATA.length > 0) {
            window.SHOP_DATA.forEach(item => {
                const cName = item.category ? item.category.name : (item.category_name || 'Lainnya');
                const cId = item.category ? item.category.id : (item.category_id || cName);
                
                if (!existingCatIds.has(String(cId)) && !existingCatNames.has(cName.toLowerCase())) {
                    existingCatIds.add(String(cId));
                    existingCatNames.add(cName.toLowerCase());
                    window.apiData.categories.push({
                        id: cId,
                        name: cName,
                        icon: getIcon(cName)
                    });
                }
            });
        }
        
        // Override highlights with shop banners
        const shopBanners = @json($shop->banners ?? []);
        if (shopBanners && shopBanners.length > 0) {
            window.apiData.highlights = shopBanners.map((banner, index) => ({
                id: 'h' + (index + 1),
                img: banner, // Use the full URL provided by Shop->banners accessor
                title: '',
                desc: ''
            })).filter(b => b.img); // Just filter out nulls/empty strings
            
            // Jika tidak ada banner valid yang diupload, kembalikan ke default agar tidak kosong
            if (window.apiData.highlights.length === 0) {
                window.apiData.highlights = [];
            }
        } else {
            window.apiData.highlights = [];
        }

        const DB = {
            get: function(tableName) {
                if (tableName === window.SHOP_SLUG + '_menu') {
                    return window.SHOP_DATA.map(item => ({
                        id: item.id,
                        name: item.name,
                        price: Number(item.price),
                        desc: item.description,
                        categoryId: item.category_id || (item.category ? item.category.id : item.category_name),
                        categoryName: item.category ? item.category.name : (item.category_name || ''),
                        img: item.image_url ? item.image_url : '/Assests/null image.webp',
                        soldOut: item.is_sold_out
                    }));
                }
                return localStorage.getItem(tableName) ? JSON.parse(localStorage.getItem(tableName)) : [];
            },
            createOrder: function(table, name, email, phone, paymentMethod, items, total) {
                let orders = this.get(window.SHOP_SLUG + '_orders') || [];
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
                localStorage.setItem(window.SHOP_SLUG + '_orders', JSON.stringify(orders));

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
                        localStorage.setItem(window.SHOP_SLUG + '_last_order', JSON.stringify(data.order));
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
    <script src="{{ asset('js/store.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
