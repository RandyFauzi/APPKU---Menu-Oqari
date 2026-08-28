    <script src="{{ asset('js/data.js') }}"></script>
    <script>
        window.SHOP_DATA = @json($menuItems ?? []);
        window.SHOP_SLUG = '{{ $shop->slug }}';
        window.SHOP_HOME_URL = '{{ route("shop.menu", $shop->slug) }}';
        window.SHOP_CART_URL = '{{ route("shop.cart", $shop->slug) }}';
        window.SHOP_TRACKING_URL = '{{ route("shop.tracking", $shop->slug) }}';

        const DB = {
            get: function(tableName) {
                if (tableName === 'bitten_menu') {
                    return window.SHOP_DATA.map(item => ({
                        id: item.id,
                        name: item.name,
                        price: Number(item.price),
                        desc: item.description,
                        categoryId: item.category_name,
                        img: item.image_url ? '/storage/' + item.image_url : null,
                        soldOut: item.is_sold_out
                    }));
                }
                return localStorage.getItem(tableName) ? JSON.parse(localStorage.getItem(tableName)) : [];
            },
            createOrder: function(table, name, orderType, items, total) {
                let orders = this.get('bitten_orders') || [];
                const newOrder = {
                    id: 'ORD-' + Date.now(),
                    table: table,
                    customer: name,
                    type: orderType,
                    items: items,
                    total: total,
                    status: 'process',
                    timestamp: new Date().toISOString()
                };
                orders.push(newOrder);
                localStorage.setItem('bitten_orders', JSON.stringify(orders));

                fetch('/' + window.SHOP_SLUG + '/order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        table_id: table,
                        customer_name: name,
                        payment_method: orderType,
                        items: items.map(item => ({
                            id: item.id,
                            qty: item.qty,
                            notes: item.notes
                        }))
                    })
                }).then(res => res.json()).catch(err => console.error(err));
            }
        };
    </script>
    <script src="{{ asset('js/store.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
