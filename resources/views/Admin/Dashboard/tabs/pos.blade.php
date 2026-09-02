<div x-show="currentTab === 'pos'" x-cloak class="animation-fade h-full w-full flex flex-col" x-data="posSystem()">
    <!-- Top Bar: Search -->
    <div class="mb-4">
        <div class="relative w-full">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" placeholder="SEARCH MENU 🔎" class="w-full bg-white border-2 border-gray-200 rounded-xl py-3 pl-11 pr-4 text-gray-800 font-bold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-sm">
        </div>
    </div>

    <div class="flex-1 flex gap-4 min-h-0">
        <!-- Main Content Area: Categories & Products -->
        <div class="flex-1 flex bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-0">
            <!-- Sidebar: Categories -->
            <div class="w-48 bg-gray-50 border-r border-gray-200 flex flex-col overflow-y-auto hide-scroll">
                <button @click="activeCategory = 'all'" 
                        class="px-4 py-4 text-left font-bold border-b border-gray-200 transition-colors"
                        :class="activeCategory === 'all' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-600 hover:bg-gray-100'">
                    ALL MENU
                </button>
                @foreach($categories as $category)
                <button @click="activeCategory = {{ $category->id }}" 
                        class="px-4 py-4 text-left font-bold border-b border-gray-200 transition-colors uppercase"
                        :class="activeCategory === {{ $category->id }} ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-600 hover:bg-gray-100'">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-white hide-scroll">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div @click="openProductModal(product)" class="bg-white border-2 border-gray-100 rounded-xl overflow-hidden cursor-pointer hover:border-blue-500 hover:shadow-md transition-all active:scale-95 group flex flex-col">
                            <div class="h-28 bg-gray-100 w-full relative overflow-hidden">
                                <template x-if="product.image_url">
                                    <img :src="product.image_url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </template>
                                <template x-if="!product.image_url">
                                    <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                        <i class="fas fa-coffee text-4xl"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <h4 class="font-bold text-gray-800 leading-tight mb-1" x-text="product.name"></h4>
                                <p class="text-blue-600 font-extrabold" x-text="formatRupiah(product.price)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right Pane: Cart -->
        <div class="w-96 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-0">
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="font-black text-lg text-gray-800"><i class="fas fa-shopping-cart mr-2 text-blue-500"></i> CART</h3>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-red-500 hover:text-red-700 text-sm font-bold">CLEAR</button>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-3 hide-scroll">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-basket-shopping text-5xl mb-3 opacity-20"></i>
                        <p class="font-bold">Cart is empty</p>
                    </div>
                </template>
                
                <div class="space-y-2">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="bg-white border border-gray-100 rounded-lg p-3 shadow-sm relative group">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm leading-tight" x-text="item.product.name"></p>
                                    
                                    <!-- Options summary -->
                                    <div class="text-[10px] text-gray-500 mt-1 font-medium leading-tight">
                                        <template x-if="item.variant">
                                            <div>Variant: <span x-text="item.variant.name"></span></div>
                                        </template>
                                        <template x-if="item.modifiers && item.modifiers.length > 0">
                                            <div>Mods: <span x-text="item.modifiers.map(m => m.name).join(', ')"></span></div>
                                        </template>
                                        <template x-if="item.notes">
                                            <div class="text-orange-500">Note: <span x-text="item.notes"></span></div>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="font-bold text-gray-800 text-sm" x-text="formatRupiah(item.subtotal)"></p>
                                </div>
                            </div>
                            
                            <!-- Qty Controls -->
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                                <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                                    <button @click="updateQty(index, item.qty - 1)" class="w-6 h-6 rounded bg-white shadow-sm text-gray-600 hover:text-red-500 flex items-center justify-center font-bold">-</button>
                                    <span class="w-6 text-center font-bold text-sm" x-text="item.qty"></span>
                                    <button @click="updateQty(index, item.qty + 1)" class="w-6 h-6 rounded bg-white shadow-sm text-gray-600 hover:text-blue-500 flex items-center justify-center font-bold">+</button>
                                </div>
                                <button @click="removeFromCart(index)" class="text-red-400 hover:text-red-600 p-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart Footer (Totals & Pay) -->
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-gray-600">TOTAL</span>
                    <span class="text-2xl font-black text-blue-600" x-text="formatRupiah(cartTotal)"></span>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <button @click="checkout('CASH')" class="py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold text-sm shadow-sm active:scale-95 transition flex flex-col items-center justify-center">
                        <i class="fas fa-money-bill-wave mb-1"></i> CASH
                    </button>
                    <button @click="checkout('QRIS')" class="py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm shadow-sm active:scale-95 transition flex flex-col items-center justify-center">
                        <i class="fas fa-qrcode mb-1"></i> QRIS
                    </button>
                    <button @click="checkout('CARD')" class="py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-bold text-sm shadow-sm active:scale-95 transition flex flex-col items-center justify-center">
                        <i class="fas fa-credit-card mb-1"></i> CARD
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal (Modifiers & Variants) -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col max-h-full" @click.away="closeModal()">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="font-black text-lg text-gray-800" x-text="selectedProduct?.name"></h3>
                <button @click="closeModal()" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-4 overflow-y-auto">
                <!-- Variants -->
                <template x-if="selectedProduct?.variants && selectedProduct.variants.length > 0">
                    <div class="mb-4">
                        <h4 class="font-bold text-sm text-gray-700 mb-2 uppercase">Size / Variant</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="variant in selectedProduct.variants" :key="variant.id">
                                <label class="border rounded-xl p-3 cursor-pointer transition flex justify-between items-center"
                                       :class="selectedVariant === variant ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'">
                                    <span class="font-bold text-sm" x-text="variant.name"></span>
                                    <span class="text-xs text-blue-600 font-bold" x-show="variant.price_adjustment > 0" x-text="'+' + formatRupiah(variant.price_adjustment)"></span>
                                    <input type="radio" :value="variant" x-model="selectedVariant" class="hidden">
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Modifiers -->
                <template x-if="selectedProduct?.modifier_groups && selectedProduct.modifier_groups.length > 0">
                    <div>
                        <template x-for="group in selectedProduct.modifier_groups" :key="group.id">
                            <div class="mb-4">
                                <h4 class="font-bold text-sm text-gray-700 mb-2 uppercase flex justify-between">
                                    <span x-text="group.name"></span>
                                    <span x-show="group.is_required" class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">REQUIRED</span>
                                </h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="modifier in group.modifiers" :key="modifier.id">
                                        <label class="border rounded-xl p-3 cursor-pointer transition flex justify-between items-center"
                                               :class="selectedModifiers.some(m => m.id === modifier.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'">
                                            <span class="font-bold text-sm" x-text="modifier.name"></span>
                                            <span class="text-xs text-blue-600 font-bold" x-show="modifier.price_adjustment > 0" x-text="'+' + formatRupiah(modifier.price_adjustment)"></span>
                                            <input type="checkbox" :value="modifier" x-model="selectedModifiers" class="hidden" @change="handleModifierChange(group, modifier, $event)">
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Notes -->
                <div class="mt-4">
                    <h4 class="font-bold text-sm text-gray-700 mb-2 uppercase">Notes</h4>
                    <input type="text" x-model="itemNotes" placeholder="Contoh: Less ice, extra hot..." class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none focus:ring-0">
                </div>
            </div>

            <!-- Footer Add -->
            <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="modalQty = Math.max(1, modalQty - 1)" class="w-10 h-10 bg-white border border-gray-200 rounded-xl font-bold text-lg hover:bg-gray-100">-</button>
                    <span class="font-black text-xl w-6 text-center" x-text="modalQty"></span>
                    <button @click="modalQty++" class="w-10 h-10 bg-white border border-gray-200 rounded-xl font-bold text-lg hover:bg-gray-100">+</button>
                </div>
                <button @click="addToCart()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold flex gap-2 shadow-sm active:scale-95 transition">
                    <span>ADD</span>
                    <span x-text="formatRupiah(calculateModalTotal())"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('posSystem', () => ({
        products: {!! json_encode($menuItems ?? []) !!},
        search: '',
        activeCategory: 'all',
        cart: [],
        
        // Modal state
        isModalOpen: false,
        selectedProduct: null,
        selectedVariant: null,
        selectedModifiers: [],
        itemNotes: '',
        modalQty: 1,

        filteredProducts() {
            return this.products.filter(p => {
                const matchCat = this.activeCategory === 'all' || p.category_id == this.activeCategory;
                const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase());
                return matchCat && matchSearch;
            });
        },

        get cartTotal() {
            return this.cart.reduce((total, item) => total + item.subtotal, 0);
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        openProductModal(product) {
            this.selectedProduct = product;
            this.selectedVariant = (product.variants && product.variants.length > 0) ? product.variants[0] : null;
            this.selectedModifiers = [];
            this.itemNotes = '';
            this.modalQty = 1;
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        handleModifierChange(group, modifier, event) {
            if (group.max_choices === 1 && event.target.checked) {
                // Remove other modifiers from same group
                this.selectedModifiers = this.selectedModifiers.filter(m => {
                    const isInSameGroup = group.modifiers.some(gm => gm.id === m.id);
                    return !isInSameGroup || m.id === modifier.id;
                });
            }
        },

        calculateModalTotal() {
            if (!this.selectedProduct) return 0;
            let price = this.selectedProduct.price;
            if (this.selectedVariant) price += this.selectedVariant.price_adjustment;
            this.selectedModifiers.forEach(m => price += m.price_adjustment);
            return price * this.modalQty;
        },

        addToCart() {
            // Validation for required groups could be added here
            let price = this.selectedProduct.price;
            if (this.selectedVariant) price += this.selectedVariant.price_adjustment;
            this.selectedModifiers.forEach(m => price += m.price_adjustment);
            
            this.cart.push({
                product: this.selectedProduct,
                variant: this.selectedVariant,
                modifiers: [...this.selectedModifiers],
                notes: this.itemNotes,
                qty: this.modalQty,
                unit_price: price,
                subtotal: price * this.modalQty
            });
            this.closeModal();
        },

        updateQty(index, newQty) {
            if (newQty < 1) {
                this.removeFromCart(index);
                return;
            }
            this.cart[index].qty = newQty;
            this.cart[index].subtotal = this.cart[index].unit_price * newQty;
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Clear the entire cart?')) {
                this.cart = [];
            }
        },

        async checkout(paymentMethod) {
            if (this.cart.length === 0) return alert('Cart is empty!');
            
            const payload = {
                table_id: 'Walk-in',
                customer_name: 'Guest',
                payment_method: paymentMethod,
                items: this.cart.map(item => ({
                    id: item.product.id,
                    qty: item.qty,
                    notes: item.notes,
                    variant_id: item.variant ? item.variant.id : null,
                    modifiers: item.modifiers ? item.modifiers.map(m => m.id) : []
                }))
            };

            try {
                const response = await fetch('/' + '{{ $shop->slug ?? "" }}' + '/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Transaction Success! Order #' + data.order.id);
                    this.cart = [];
                } else {
                    alert('Failed: ' + data.message);
                }
            } catch (e) {
                alert('Network Error');
            }
        }
    }));
});
</script>
