    <script>
        window.SHOP_CATEGORIES = @json($categories ?? []);
        window.SHOP_DATA = @json($menuItems ?? []);
        window.SHOP_SLUG = '{{ $shop->slug }}';
        window.SHOP_DISCOUNT_SETTINGS = @json($shop->discount_settings ?? []);
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
            'kopi': 'fa-coffee',
            'tea': 'fa-leaf',
            'teh': 'fa-leaf',
            'foods': 'fa-utensils',
            'food': 'fa-utensils',
            'makanan': 'fa-utensils',
            'snacks': 'fa-cookie-bite',
            'snack': 'fa-cookie-bite',
            'cemilan': 'fa-cookie-bite',
            'sweets': 'fa-ice-cream',
            'sweet': 'fa-ice-cream',
            'dessert': 'fa-ice-cream',
            'beverages': 'fa-glass-water',
            'beverage': 'fa-glass-water',
            'minuman': 'fa-glass-water',
            'drinks': 'fa-glass-water',
            'drink': 'fa-glass-water'
        };
        
        const getIcon = (name) => {
            if (!name) return 'fa-layer-group';
            const lower = name.toLowerCase().trim();
            if (iconMap[lower]) return iconMap[lower];

            if (lower.includes('kopi') || lower.includes('coffee') || lower.includes('espresso') || lower.includes('latte') || lower.includes('cappuccino') || lower.includes('americano')) return 'fa-coffee';
            if (lower.includes('teh') || lower.includes('tea') || lower.includes('matcha')) return 'fa-leaf';
            if (lower.includes('jus') || lower.includes('juice') || lower.includes('smoothie')) return 'fa-blender';
            if (lower.includes('minum') || lower.includes('drink') || lower.includes('beverage') || lower.includes('boba') || lower.includes('milk') || lower.includes('susu') || lower.includes('fresh')) return 'fa-glass-water';
            if (lower.includes('mocktail') || lower.includes('cocktail') || lower.includes('syrup') || lower.includes('soda') || lower.includes('sparkling')) return 'fa-cocktail';
            if (lower.includes('snack') || lower.includes('cemil') || lower.includes('goreng') || lower.includes('fries') || lower.includes('kentang') || lower.includes('chips') || lower.includes('camilan')) return 'fa-cookie-bite';
            if (lower.includes('roti') || lower.includes('toast') || lower.includes('bread') || lower.includes('bakery') || lower.includes('pastry') || lower.includes('croissant')) return 'fa-bread-slice';
            if (lower.includes('cake') || lower.includes('kue') || lower.includes('dessert') || lower.includes('sweet') || lower.includes('manis')) return 'fa-cake-candles';
            if (lower.includes('ice cream') || lower.includes('es krim') || lower.includes('gelato')) return 'fa-ice-cream';
            if (lower.includes('nasi') || lower.includes('rice') || lower.includes('bowl') || lower.includes('mie') || lower.includes('noodle') || lower.includes('ramen') || lower.includes('pasta') || lower.includes('spaghetti')) return 'fa-bowl-rice';
            if (lower.includes('makan') || lower.includes('food') || lower.includes('main') || lower.includes('lunch') || lower.includes('dinner') || lower.includes('steak') || lower.includes('burger') || lower.includes('ayam') || lower.includes('daging')) return 'fa-utensils';
            if (lower.includes('paket') || lower.includes('combo') || lower.includes('bundle') || lower.includes('hemat')) return 'fa-box-open';
            if (lower.includes('promo') || lower.includes('diskon') || lower.includes('sale') || lower.includes('special')) return 'fa-tags';
            if (lower.includes('signature') || lower.includes('rekomendasi') || lower.includes('favorit') || lower.includes('best') || lower.includes('unggulan')) return 'fa-award';

            return 'fa-layer-group';
        };
        
        window.apiData.categories = [{ id: 'all', name: 'All Menu', icon: 'fa-star' }];
        const existingCatIds = new Set(['all']);
        const existingCatNames = new Set(['all menu']);

        if (window.SHOP_CATEGORIES && window.SHOP_CATEGORIES.length > 0) {
            window.SHOP_CATEGORIES.forEach(cat => {
                window.apiData.categories.push({
                    id: cat.id,
                    name: cat.name,
                    icon: cat.icon || getIcon(cat.name)
                });
                existingCatIds.add(String(cat.id));
                existingCatNames.add(cat.name.toLowerCase().trim());
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
                    return window.SHOP_DATA.map(item => {
                        // Strictly validate category against existing database categories
                        let matchedCat = null;
                        if (item.category && existingCatIds.has(String(item.category.id))) {
                            matchedCat = item.category;
                        } else if (item.category_id && existingCatIds.has(String(item.category_id))) {
                            matchedCat = (window.SHOP_CATEGORIES || []).find(c => String(c.id) === String(item.category_id));
                        } else if (item.category_name && existingCatNames.has(String(item.category_name).toLowerCase().trim())) {
                            matchedCat = (window.SHOP_CATEGORIES || []).find(c => c.name.toLowerCase().trim() === String(item.category_name).toLowerCase().trim());
                        }

                        return {
                            id: item.id,
                            name: item.name,
                            price: Number(item.price),
                            discount_price: item.discount_price !== null && item.discount_price !== undefined ? Number(item.discount_price) : null,
                            is_discount_active: Boolean(item.is_discount_active),
                            effective_price: Number(item.effective_price || item.price),
                            discount_percentage: item.discount_percentage || null,
                            has_active_discount: Boolean(item.has_active_discount),
                            desc: item.description,
                            categoryId: matchedCat ? matchedCat.id : null,
                            categoryName: matchedCat ? matchedCat.name : '',
                            img: item.image_url ? item.image_url : '/Assests/null image.webp',
                            soldOut: item.is_sold_out,
                            isBestSeller: Boolean(item.is_best_seller),
                            modifier_groups: item.modifier_groups || item.modifierGroups || []
                        };
                    });
                }
                return localStorage.getItem(tableName) ? JSON.parse(localStorage.getItem(tableName)) : [];
            },
            createOrder: function(table, name, email, phone, paymentMethod, items, total, fulfillmentType = 'DINE_IN') {
                let orders = this.get(window.SHOP_SLUG + '_orders') || [];
                const newOrder = {
                    id: 'ORD-' + Date.now(),
                    table: table,
                    customer: name,
                    email: email,
                    phone: phone,
                    payment_method: paymentMethod,
                    fulfillment_type: fulfillmentType,
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
                        table_id: table || null,
                        customer_name: name,
                        customer_email: email,
                        customer_phone: phone,
                        payment_method: paymentMethod,
                    fulfillment_type: fulfillmentType,
                        items: items.map(item => ({
                            id: item.id,
                            qty: item.qty,
                            notes: item.notes,
                            modifiers: item.modifier_ids || []
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
