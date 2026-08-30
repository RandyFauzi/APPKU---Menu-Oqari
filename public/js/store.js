// C:\laragon\www\Goodwill\js\store.js

const CartStore = {
    getKey: () => 'gw_cart',
    
    get: () => {
        const data = localStorage.getItem(CartStore.getKey());
        return data ? JSON.parse(data) : [];
    },
    
    set: (cartArray) => {
        localStorage.setItem(CartStore.getKey(), JSON.stringify(cartArray));
    },
    
    // Add sekarang mendukung kustomisasi (variant & notes)
    add: (item, variants = {}, notes = '') => {
        const cart = CartStore.get();
        // Buat ID unik berdasarkan kombinasi item + varian + catatan
        const cartId = `${item.id}_${JSON.stringify(variants)}_${notes}`;
        
        const existing = cart.find(c => c.cartId === cartId);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ ...item, cartId, variants, notes, qty: 1 });
        }
        CartStore.set(cart);
    },
    
    updateQty: (index, change) => {
        const cart = CartStore.get();
        cart[index].qty += change;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        CartStore.set(cart);
        return cart;
    },
    
    clear: () => {
        localStorage.removeItem(CartStore.getKey());
    },
    
    getTotalQty: () => {
        return CartStore.get().reduce((sum, item) => sum + item.qty, 0);
    },
    
    getSubtotal: () => {
        return CartStore.get().reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
};
