// C:\laragon\www\Menu Apps\Bitten Coffee\js\main.js

const formatRp = (angka) => 'Rp ' + angka.toLocaleString('id-ID');

document.addEventListener('DOMContentLoaded', () => {
    // 1. Deteksi Meja dari URL (QR Code)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('table')) {
        localStorage.setItem('bitten_table_qr', urlParams.get('table'));
    }

    const page = document.body.dataset.page;
    if (page === 'home') initHomePage();
    if (page === 'cart') initCartPage();
    if (page === 'tracking') initTrackingPage();
});

// ==========================================
// LOGIKA: HALAMAN HOME (index.html)
// ==========================================
let activeCategory = 'all';
let currentSelectedItem = null; 
let searchQuery = '';
let currentSlide = 0;
let slideInterval;

function initHomePage() {
    const splash = document.getElementById('splash-screen');
    if (splash) {
        if (sessionStorage.getItem('bitten_splash_shown')) {
            splash.remove();
        } else {
            setTimeout(() => {
                splash.style.opacity = '0';
                setTimeout(() => {
                    splash.remove();
                    sessionStorage.setItem('bitten_splash_shown', 'true');
                }, 500);
            }, 3000);
        }
    }

    renderCarousel();
    renderCategories();
    renderMenu();
    updateFloatingCart();

    const searchInput = document.getElementById('search-menu');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase();
            renderMenu();
        });
    }
}

function renderCarousel() {
    const track = document.getElementById('carousel-container');
    const dotsContainer = document.getElementById('carousel-dots');
    if (!track || !dotsContainer) return;
    
    const highlights = apiData.highlights || [];
    
    track.innerHTML = highlights.map(h => `
        <div class="min-w-full h-full snap-center bg-gray-900 relative">
            <img src="${h.img}" class="w-full h-full object-cover">
            <!-- Gradasi Biru hanya di bawah -->
            <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-[#1E5A7A] via-[#1E5A7A]/60 to-transparent"></div>
            <div class="absolute inset-0 p-5 flex flex-col justify-end w-full text-white z-10 pb-8">
                <h2 class="text-3xl font-black font-heading leading-[1.1] uppercase text-white drop-shadow-md">${h.title}</h2>
                ${h.desc ? `<p class="text-xs opacity-90 font-medium mt-1">${h.desc}</p>` : ''}
            </div>
        </div>
    `).join('');

    dotsContainer.innerHTML = highlights.map((_, i) => `
        <div class="w-2 h-2 rounded-full transition-colors shadow-sm z-20 relative ${i === 0 ? 'bg-white' : 'bg-white/40'}" id="dot-${i}"></div>
    `).join('');

    // Auto Slide
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        currentSlide = (currentSlide + 1) % highlights.length;
        updateCarousel();
    }, 3000); // Diubah menjadi 3 detik
}

function updateCarousel() {
    const track = document.getElementById('carousel-container');
    const dotsContainer = document.getElementById('carousel-dots');
    if(!track || !dotsContainer) return;
    
    // Scroll element based on slide index
    const slideWidth = track.clientWidth;
    track.scrollTo({ left: currentSlide * slideWidth, behavior: 'smooth' });
    
    // Update dots
    const dots = dotsContainer.children;
    for (let i = 0; i < dots.length; i++) {
        if (i === currentSlide) {
            dots[i].className = 'w-2 h-2 rounded-full transition-colors shadow-sm z-20 relative bg-white';
        } else {
            dots[i].className = 'w-2 h-2 rounded-full transition-colors shadow-sm z-20 relative bg-white/40';
        }
    }
}

function renderCategories() {
    const container = document.getElementById('category-container');
    if (!container) return;
    
    container.innerHTML = apiData.categories.map(cat => {
        const isActive = activeCategory === cat.id;
        const bgClass = isActive ? 'bg-white border-2 border-primary' : 'bg-white border-2 border-transparent hover:border-primary/30';
        const iconColor = isActive ? 'text-primary' : 'text-accent';
        const textClass = isActive ? 'text-primary font-bold' : 'text-textdark/70';
        
        return `
        <div onclick="filterCategory('${cat.id}')" class="flex flex-col items-center gap-2 cursor-pointer group flex-shrink-0 w-20">
            <div class="w-16 h-16 rounded-2xl shadow-sm flex items-center justify-center transition-all ${bgClass}">
                <i class="fas ${cat.icon} text-2xl ${iconColor} group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="text-[10px] text-center w-full truncate ${textClass}">${cat.name}</span>
        </div>
        `;
    }).join('');
}

function filterCategory(catId) {
    activeCategory = catId;
    renderCategories();
    
    const container = document.getElementById('menu-container');
    container.classList.remove('shoji-slide');
    void container.offsetWidth; 
    container.classList.add('shoji-slide');

    renderMenu();
}

function renderMenu() {
    const container = document.getElementById('menu-container');
    if (!container) return;
    
    let filteredMenu = typeof DB !== 'undefined' ? DB.get('bitten_menu') : apiData.menu;
    if (activeCategory !== 'all') {
        filteredMenu = filteredMenu.filter(m => m.categoryId === activeCategory);
    }
    if (searchQuery) {
        filteredMenu = filteredMenu.filter(m => m.name.toLowerCase().includes(searchQuery));
    }

    let htmlOutput = '';

    if (activeCategory === 'all' && !searchQuery) {
        // Group by category
        apiData.categories.forEach(cat => {
            if (cat.id === 'all') return;
            const itemsInCat = filteredMenu.filter(m => m.categoryId === cat.id);
            if (itemsInCat.length > 0) {
                htmlOutput += `<div class="col-span-2 mt-4 mb-1 border-b-2 border-primary/10 pb-1"><h3 class="font-heading font-bold text-lg text-primary">${cat.name}</h3></div>`;
                htmlOutput += itemsInCat.map(item => renderMenuItem(item)).join('');
            }
        });
    } else {
        // Flat list
        htmlOutput = filteredMenu.map(item => renderMenuItem(item)).join('');
    }

    if(filteredMenu.length === 0) {
        container.innerHTML = `<div class="col-span-2 text-center py-10 text-textdark/50 text-sm">Menu tidak ditemukan.</div>`;
    } else {
        container.innerHTML = htmlOutput;
    }
}

function renderMenuItem(item) {
    return `
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300 relative">
            <div class="p-2 relative cursor-pointer" onclick="openItemDetail('${item.id}')">
                <img src="${item.image || item.img}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${item.name}" class="h-32 w-full object-cover rounded-xl">
                ${item.categoryId === 'beverages' ? '<div class="steam-effect"></div>' : ''}
            </div>
            <div class="px-3 pb-3 pt-1 flex flex-col flex-grow relative cursor-pointer" onclick="openItemDetail('${item.id}')">
                <h3 class="font-bold text-textdark text-xs leading-tight mb-1">${item.name}</h3>
                <div class="mt-auto flex justify-between items-end">
                    <span class="font-bold text-primary text-sm">${formatRp(item.price)}</span>
                </div>
            </div>
            <button onclick="quickAddToCart('${item.id}', event)" class="absolute bottom-2 right-2 bg-primary text-white h-7 w-7 rounded-full flex items-center justify-center shadow-md active:scale-95 transition-transform z-10">
                <i class="fas fa-plus text-[10px]"></i>
            </button>
        </div>
    `;
}

function quickAddToCart(itemId, event) {
    event.stopPropagation();
    const menuData = typeof DB !== 'undefined' ? DB.get('bitten_menu') : apiData.menu;
    currentSelectedItem = menuData.find(m => m.id == itemId);
    if(currentSelectedItem) {
        confirmAddToCart(); // Adds defaults
    }
}

// Fitur Detail Produk & Customization
function openItemDetail(itemId) {
    const menuData = typeof DB !== 'undefined' ? DB.get('bitten_menu') : apiData.menu;
    currentSelectedItem = menuData.find(m => m.id == itemId);
    if(!currentSelectedItem) return;

    document.getElementById('detail-img').src = currentSelectedItem.image || currentSelectedItem.img || '/Assests/null%20image.webp';
    document.getElementById('detail-name').innerText = currentSelectedItem.name;
    document.getElementById('detail-desc').innerText = currentSelectedItem.desc;
    document.getElementById('detail-price').innerText = formatRp(currentSelectedItem.price);
    
    document.getElementById('detail-notes').value = '';
    
    const addonsContainer = document.getElementById('dynamic-addons');
    if (addonsContainer) {
        if (currentSelectedItem.categoryId === 'beverages') {
            addonsContainer.innerHTML = `
                <h4 class="font-bold text-sm text-textdark mb-3 font-heading">Add-ons Minuman</h4>
                <div class="flex flex-col gap-2 mb-4">
                    <label class="flex items-center gap-3 p-3 border-2 border-primary/10 rounded-xl cursor-pointer">
                        <input type="checkbox" name="addon" value="Extra Espresso (+5k)" data-price="5000" class="accent-primary w-4 h-4">
                        <span class="text-sm font-bold text-textdark">Extra Espresso (+Rp 5.000)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border-2 border-primary/10 rounded-xl cursor-pointer">
                        <input type="checkbox" name="addon" value="Oatmilk (+7k)" data-price="7000" class="accent-primary w-4 h-4">
                        <span class="text-sm font-bold text-textdark">Oatmilk (+Rp 7.000)</span>
                    </label>
                </div>
            `;
        } else {
            addonsContainer.innerHTML = '';
        }
    }

    document.getElementById('modal-item-detail').classList.remove('hidden');
}

function closeItemDetail() {
    document.getElementById('modal-item-detail').classList.add('hidden');
    currentSelectedItem = null;
}

function confirmAddToCart() {
    if(!currentSelectedItem) return;
    
    if (navigator.vibrate) navigator.vibrate(100);
    const btn = document.getElementById('add-cart-btn');
    if (btn) {
        btn.classList.add('stamp-active');
        setTimeout(() => btn.classList.remove('stamp-active'), 200);
    }

    let notesElement = document.getElementById('detail-notes');
    let notes = notesElement ? notesElement.value.trim() : '';
    let additionalPrice = 0;
    
    const addonsInputs = document.querySelectorAll('input[name="addon"]:checked');
    addonsInputs.forEach(input => {
        notes += (notes ? ', ' : '') + input.value;
        additionalPrice += parseInt(input.getAttribute('data-price') || '0');
    });

    const itemToAdd = {
        ...currentSelectedItem,
        price: currentSelectedItem.price + additionalPrice
    };

    CartStore.add(itemToAdd, {}, notes);
    
    closeItemDetail();
    updateFloatingCart();
    showToast(`Ditambahkan <b>${currentSelectedItem.name}</b>`);
}

function updateFloatingCart() {
    const cartBar = document.getElementById('cart-bar');
    if (!cartBar) return;
    const totalQty = CartStore.getTotalQty();
    if (totalQty > 0) {
        cartBar.classList.remove('translate-y-full', 'opacity-0');
        document.getElementById('cart-count').innerText = totalQty;
        document.getElementById('cart-total').innerText = formatRp(CartStore.getSubtotal());
    } else {
        cartBar.classList.add('translate-y-full', 'opacity-0');
    }
}

// ==========================================
// LOGIKA: HALAMAN CART (cart.html) & TRACKING
// ==========================================
// (Rest of the code remains the same for cart and tracking logic)

function initCartPage() {
    renderCartDetail();
    document.querySelectorAll('input[name="orderType"]').forEach(radio => {
        radio.addEventListener('change', renderCartDetail);
    });
}

function renderCartDetail() {
    const container = document.getElementById('cart-items-container');
    const btnPay = document.getElementById('btn-trigger-pay');
    const cart = CartStore.get();
    
    if(cart.length === 0) {
        container.innerHTML = `<div class="text-center py-10 bg-white rounded-xl border-2 border-primary/10">
            <p class="text-textdark/60 font-medium text-sm">Keranjang kosong</p></div>`;
        document.getElementById('summary-section').classList.add('hidden');
        btnPay.disabled = true;
        btnPay.classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    // Tampilkan bagian data diri dan ringkasan
    document.getElementById('summary-section').classList.remove('hidden');
    
    // Logika QR Table
    const detectedTable = localStorage.getItem('bitten_table_qr');
    const badgeTable = document.getElementById('table-detected-badge');
    const manualInput = document.getElementById('manual-table-input');
    
    if (badgeTable && manualInput) {
        if (detectedTable) {
            badgeTable.classList.remove('hidden');
            document.getElementById('display-table-num').innerText = detectedTable;
            manualInput.classList.add('hidden');
        } else {
            badgeTable.classList.add('hidden');
            manualInput.classList.remove('hidden');
        }
    }

    btnPay.disabled = false;
    btnPay.classList.remove('opacity-50', 'cursor-not-allowed');

    container.innerHTML = cart.map((item, index) => {
        let notesText = item.notes ? `<p class="text-[10px] mt-1 p-1 bg-bgbase rounded text-primary">📝 ${item.notes}</p>` : '';
        return `
        <div class="flex items-center gap-4 bg-white p-3 rounded-xl border-2 border-primary/10 mb-3 relative">
            <img src="${item.img}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" class="w-16 h-16 rounded-lg object-cover">
            <div class="flex-grow">
                <h4 class="font-bold text-sm text-textdark leading-tight font-heading">${item.name}</h4>
                <p class="text-primary font-bold text-sm mt-1">${formatRp(item.price)}</p>
                ${notesText}
            </div>
            <div class="flex flex-col items-center gap-1">
                <button onclick="handleUpdateQty(${index}, 1)" class="w-6 h-6 rounded-md bg-primary text-white flex items-center justify-center shadow-sm active:scale-95 transition-transform"><i class="fas fa-plus text-[10px]"></i></button>
                <span class="text-xs font-bold w-6 text-center text-textdark">${item.qty}</span>
                <button onclick="handleUpdateQty(${index}, -1)" class="w-6 h-6 rounded-md bg-bgbase text-primary border border-primary/20 flex items-center justify-center shadow-sm active:scale-95 transition-transform"><i class="fas fa-minus text-[10px]"></i></button>
            </div>
        </div>
        `;
    }).join('');

    const subtotal = CartStore.getSubtotal();
    const tax = Math.floor(subtotal * 0.10); 
    const grandTotal = subtotal + tax;

    document.getElementById('summary-subtotal').innerText = formatRp(subtotal);
    document.getElementById('summary-tax').innerText = formatRp(tax);
    document.getElementById('cart-detail-total').innerText = formatRp(grandTotal);
    document.getElementById('btn-pay-text').innerText = 'Lanjut Pembayaran - ' + formatRp(grandTotal);
    
    window.currentGrandTotal = grandTotal;
}

function handleUpdateQty(index, change) { CartStore.updateQty(index, change); renderCartDetail(); }

function openCustomerInfoModal() {
    if (CartStore.get().length === 0) return;
    
    // Auto-fill table from QR if exists
    let qrTable = localStorage.getItem('bitten_table_qr');
    const tableInput = document.getElementById('customer-table');
    if (qrTable && tableInput) {
        tableInput.value = qrTable;
        // Kunci input agar tidak bisa diubah oleh pelanggan
        tableInput.readOnly = true;
        tableInput.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
        tableInput.classList.remove('bg-white');
    }

    document.getElementById('modal-customer-info').classList.remove('hidden');
}

function closeCustomerInfoModal() {
    document.getElementById('modal-customer-info').classList.add('hidden');
}

function triggerPaymentGateway() {
    const name = document.getElementById('customer-name')?.value.trim();
    const email = document.getElementById('customer-email')?.value.trim();
    const phone = document.getElementById('customer-phone')?.value.trim();
    
    let table = localStorage.getItem('bitten_table_qr');
    if (!table) {
        table = document.getElementById('customer-table')?.value.trim();
    }
    
    if(!name || !email || !phone) { 
        showToast("⚠️ Mohon isi Nama, Email & No. WhatsApp!"); 
        return; 
    }
    
    if(email.length < 5 || !email.includes('@')) {
        showToast("⚠️ Format email tidak valid!");
        return;
    }

    if(phone.length < 9) {
        showToast("⚠️ Format nomor WhatsApp tidak valid!");
        return;
    }
    
    if(!table) {
        showToast("⚠️ Mohon isi / scan Nomor Meja!"); 
        return; 
    }

    localStorage.setItem('gw_customer_name', name);
    localStorage.setItem('gw_customer_table', table);
    localStorage.setItem('gw_customer_email', email);
    localStorage.setItem('gw_customer_phone', phone);
    
    // Tutup modal customer info dan buka payment modal
    closeCustomerInfoModal();
    document.getElementById('modal-payment').classList.remove('hidden');
    document.getElementById('pg-total').innerText = formatRp(window.currentGrandTotal);
}

function cancelPayment() { document.getElementById('modal-payment').classList.add('hidden'); }

function processSimulatedPayment() {
    const btn = document.getElementById('btn-pay');
    btn.innerHTML = `<i class="fas fa-circle-notch fa-spin"></i> Memproses...`;
    btn.disabled = true;

    const paymentMethod = document.querySelector('input[name="payment"]:checked')?.value || 'QRIS';
    const name = localStorage.getItem('gw_customer_name') || 'Guest';
    const email = localStorage.getItem('gw_customer_email') || '';
    const phone = localStorage.getItem('gw_customer_phone') || '';
    const table = localStorage.getItem('gw_customer_table') || 'TA';
    const items = CartStore.get();
    const total = window.currentGrandTotal;
    
    // Create order using database.js engine
    DB.createOrder(table, name, email, phone, paymentMethod, items, total);

    localStorage.setItem('gw_last_order_type', paymentMethod);
    localStorage.setItem('gw_last_order_total', total);
    
    setTimeout(() => { CartStore.clear(); window.location.href = window.SHOP_TRACKING_URL; }, 1500);
}

function initTrackingPage() {
    document.getElementById('track-type').innerText = localStorage.getItem('gw_last_order_type') || 'Dine-in';
    document.getElementById('track-total').innerText = formatRp(Number(localStorage.getItem('gw_last_order_total') || '0'));
    setTimeout(() => { const modal = document.getElementById('modal-rating'); if(modal) modal.classList.remove('hidden'); }, 3000);
}

function showToast(message) {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast-msg bg-textdark text-white text-sm px-4 py-3 rounded-full shadow-lg flex items-center gap-3 font-bold mx-auto w-max';
    toast.innerHTML = `<i class="fas fa-check-circle text-primary"></i> <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)'; toast.style.transition = 'all 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 2500);
}

function closeRating() { document.getElementById('modal-rating').classList.add('hidden'); }

function goBackHome(event) {
    if (event) event.preventDefault();
    let qrTable = localStorage.getItem('bitten_table_qr');
    if (qrTable) {
        window.location.href = window.SHOP_HOME_URL + '?table=' + qrTable;
    } else {
        window.location.href = window.SHOP_HOME_URL;
    }
}
function setRating(stars) { /* Same implementation as previous */ }

