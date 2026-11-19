function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);
}

function safeImageUrl(value) {
    try {
        const url = new URL(value || '/Assests/null%20image.webp', window.location.origin);
        return ['http:', 'https:'].includes(url.protocol) ? escapeHtml(url.href) : '/Assests/null%20image.webp';
    } catch {
        return '/Assests/null%20image.webp';
    }
}

// C:\laragon\www\Menu Apps\Bitten Coffee\js\main.js

const formatRp = (angka) => 'Rp ' + Number(angka || 0).toLocaleString('id-ID');

// Safe helpers to retrieve API data
function getApiCategories() {
    if (typeof window !== 'undefined' && window.apiData && Array.isArray(window.apiData.categories)) {
        return window.apiData.categories;
    }
    if (typeof apiData !== 'undefined' && Array.isArray(apiData.categories)) {
        return apiData.categories;
    }
    return [];
}

function getApiHighlights() {
    if (typeof window !== 'undefined' && window.apiData && Array.isArray(window.apiData.highlights)) {
        return window.apiData.highlights;
    }
    if (typeof apiData !== 'undefined' && Array.isArray(apiData.highlights)) {
        return apiData.highlights;
    }
    return [];
}

function getApiMenu() {
    const slug = (typeof window !== 'undefined' && window.SHOP_SLUG) ? window.SHOP_SLUG : 'default';
    if (typeof DB !== 'undefined' && typeof DB.get === 'function') {
        const items = DB.get(slug + '_menu');
        if (Array.isArray(items)) return items;
    }
    if (typeof window !== 'undefined' && window.apiData && Array.isArray(window.apiData.menu)) {
        return window.apiData.menu;
    }
    if (typeof apiData !== 'undefined' && Array.isArray(apiData.menu)) {
        return apiData.menu;
    }
    return [];
}

// Global state variables
let activeCategory = 'all';
let currentSelectedItem = null; 
let searchQuery = '';
let currentSlide = 0;
let slideInterval = null;
let isProgrammaticScroll = false;
let scrollTimeout = null;

function initHomePage() {
    const splash = document.getElementById('splash-screen');
    if (splash) {
        setTimeout(() => {
            splash.style.opacity = '0';
            splash.style.pointerEvents = 'none';
            setTimeout(() => {
                if (splash && splash.parentNode) splash.parentNode.removeChild(splash);
            }, 500);
        }, 800);
    }

    renderCarousel();
    renderBestSellers();
    renderCategories();
    renderMenu();
    initCategoryScrollSpy();
    updateFloatingCart();

    const searchInput = document.getElementById('search-menu');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchQuery = e.target.value.toLowerCase();
                renderMenu();
            }, 300);
        });
    }
}

function renderBestSellers() {
    const shelfContainer = document.getElementById('bestseller-shelf-container');
    if (!shelfContainer) return;

    const discountSettings = window.SHOP_DISCOUNT_SETTINGS || {};
    if (discountSettings.show_highlight_menu === false) {
        shelfContainer.classList.add('hidden');
        shelfContainer.innerHTML = '';
        return;
    }

    const menuData = getApiMenu();
    const bestSellers = menuData.filter(item => item.isBestSeller && !item.soldOut);

    if (bestSellers.length === 0) {
        shelfContainer.classList.add('hidden');
        shelfContainer.innerHTML = '';
        return;
    }

    const headingTitle = discountSettings.highlight_title || 'Menu Unggulan';

    shelfContainer.classList.remove('hidden');
    shelfContainer.innerHTML = `
        <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center gap-1.5">
                <span class="text-sm">⭐</span>
                <h3 class="font-heading font-extrabold text-base text-gray-800 tracking-tight">${escapeHtml(headingTitle)}</h3>
            </div>
            <span class="text-[10px] font-extrabold text-amber-700 bg-amber-100/90 px-2.5 py-0.5 rounded-full uppercase tracking-wider">Pilihan Cafe</span>
        </div>
        <div class="flex gap-3 overflow-x-auto hide-scroll pb-2 scroll-smooth">
            ${bestSellers.map(item => `
                <div onclick="openItemDetail('${Number(item.id)}')" role="button" tabindex="0" class="w-36 shrink-0 bg-white rounded-2xl shadow-sm border border-amber-200/80 p-2.5 flex flex-col justify-between hover:shadow-md transition-all active:scale-95 cursor-pointer relative group">
                    <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden mb-2 bg-gray-50">
                        <img src="${safeImageUrl(item.img || item.image)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover">
                        <span class="absolute top-1.5 left-1.5 bg-amber-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-sm flex items-center gap-0.5">
                            <i class="fas fa-crown text-[8px]"></i> Best
                        </span>
                    </div>
                    <div class="truncate">
                        <h4 class="font-bold text-xs text-gray-800 truncate">${escapeHtml(item.name)}</h4>
                        <div class="mt-0.5 block">${formatItemPriceHtml(item, 'text-xs')}</div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function renderCarousel() {
    const track = document.getElementById('carousel-container');
    const dotsContainer = document.getElementById('carousel-dots');
    const carouselSection = document.getElementById('promo-carousel-section') || (track ? track.closest('.px-4.mb-5') : null);
    if (!track || !dotsContainer) return;
    
    const highlights = getApiHighlights();
    if (highlights.length === 0) {
        track.innerHTML = '';
        dotsContainer.innerHTML = '';
        if (carouselSection) {
            carouselSection.classList.add('hidden');
        }
        return;
    }
    if (carouselSection) {
        carouselSection.classList.remove('hidden');
    }
    
    track.innerHTML = highlights.map(h => `
        <div class="min-w-full h-full snap-center bg-gray-900 relative">
            <img src="${safeImageUrl(h.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" class="w-full h-full object-cover">
            <!-- Dynamic primary color gradient overlay -->
            <div class="absolute inset-x-0 bottom-0 h-2/3" style="background: linear-gradient(to top, var(--color-primary, #1e5a7a) 0%, rgba(0,0,0,0.5) 60%, transparent 100%);"></div>
            <div class="absolute inset-0 p-5 flex flex-col justify-end w-full text-white z-10 pb-8">
                ${h.title ? `<h2 class="text-3xl font-black font-heading leading-[1.1] uppercase text-white drop-shadow-md">${escapeHtml(h.title)}</h2>` : ''}
                ${h.desc ? `<p class="text-xs opacity-90 font-medium mt-1">${escapeHtml(h.desc)}</p>` : ''}
            </div>
        </div>
    `).join('');

    dotsContainer.innerHTML = highlights.map((_, i) => `
        <div class="w-2 h-2 rounded-full transition-colors shadow-sm z-20 relative ${i === 0 ? 'bg-white' : 'bg-white/40'}" id="dot-${i}"></div>
    `).join('');

    // Auto Slide
    clearInterval(slideInterval);
    if (highlights.length > 1) {
        slideInterval = setInterval(() => {
            currentSlide = (currentSlide + 1) % highlights.length;
            updateCarousel();
        }, 3000);
    }
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

function isItemInCategory(item, categoryTarget) {
    if (!categoryTarget || categoryTarget === 'all' || (typeof categoryTarget === 'object' && categoryTarget.id === 'all')) {
        return true;
    }

    const targetId = typeof categoryTarget === 'object' ? categoryTarget.id : categoryTarget;
    if (targetId === 'promo-diskon') {
        return Boolean(item.has_active_discount || (item.is_discount_active && item.discount_price > 0 && item.discount_price < item.price));
    }
    let targetName = typeof categoryTarget === 'object' ? categoryTarget.name : null;

    if (!targetName) {
        const categories = getApiCategories();
        const found = categories.find(c => String(c.id).toLowerCase() === String(targetId).toLowerCase());
        if (found) targetName = found.name;
    }

    const itemCatId = item.categoryId ?? item.category_id ?? (item.category ? item.category.id : null);
    const itemCatName = item.categoryName ?? item.category_name ?? (item.category ? item.category.name : null);

    // 1. Match by ID (loose string comparison to handle 1 == '1')
    if (itemCatId !== undefined && itemCatId !== null && targetId !== undefined && targetId !== null) {
        if (String(itemCatId).trim().toLowerCase() === String(targetId).trim().toLowerCase()) {
            return true;
        }
    }

    // 2. Match by categoryName
    if (itemCatName && targetName) {
        if (String(itemCatName).trim().toLowerCase() === String(targetName).trim().toLowerCase()) {
            return true;
        }
    }

    return false;
}

function renderCategories() {
    const container = document.getElementById('category-container');
    if (!container) return;

    const discountSettings = window.SHOP_DISCOUNT_SETTINGS || {};
    const showPromoTab = discountSettings.show_discount_section !== false;
    const menuData = getApiMenu();
    const hasDiscountedItems = menuData.some(item => item.has_active_discount || (item.is_discount_active && item.discount_price > 0 && item.discount_price < item.price));

    let displayCategories = [...getApiCategories()];
    if (showPromoTab && hasDiscountedItems && !displayCategories.some(c => c.id === 'promo-diskon')) {
        const allIdx = displayCategories.findIndex(c => c.id === 'all');
        const promoCat = { id: 'promo-diskon', name: 'Promo', icon: 'fa-percentage' };
        if (allIdx !== -1) {
            displayCategories.splice(allIdx + 1, 0, promoCat);
        } else {
            displayCategories.unshift(promoCat);
        }
    }
    
    container.innerHTML = displayCategories.map(cat => {
        const isActive = String(activeCategory).toLowerCase() === String(cat.id).toLowerCase();
        const bgClass = isActive 
            ? 'bg-primary text-white shadow-md shadow-primary/25 border-primary scale-[1.03]' 
            : 'bg-white text-gray-700 border-gray-200/80 hover:border-primary/40 hover:bg-gray-50/50';
        const iconColor = isActive ? 'text-white' : 'text-accent';
        const textClass = isActive ? 'text-primary font-bold' : 'text-textdark/70 font-medium';
        
        return `
        <button type="button" 
                data-category-id="${escapeHtml(cat.id)}" 
                onclick="handleCategoryClick(this.dataset.categoryId)" 
                aria-pressed="${isActive}" 
                class="category-pill flex flex-col items-center gap-1.5 cursor-pointer group flex-shrink-0 w-20 transition-all select-none outline-none">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-200 ${bgClass} border">
                <i class="fas ${cat.icon || 'fa-utensils'} text-2xl ${iconColor} group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="text-[11px] text-center w-full truncate transition-colors ${textClass}">${escapeHtml(cat.name)}</span>
        </button>
        `;
    }).join('');

    scrollActiveCategoryIntoView(activeCategory);
}

function scrollActiveCategoryIntoView(catId) {
    const container = document.getElementById('category-container');
    if (!container) return;
    const activePill = container.querySelector(`[data-category-id="${catId}"]`);
    if (activePill) {
        const containerRect = container.getBoundingClientRect();
        const pillRect = activePill.getBoundingClientRect();
        const currentScroll = container.scrollLeft;
        const targetScroll = currentScroll + (pillRect.left - containerRect.left) - (containerRect.width / 2) + (pillRect.width / 2);
        container.scrollTo({
            left: Math.max(0, targetScroll),
            behavior: 'smooth'
        });
    }
}

function setActiveCategoryPill(catId) {
    if (String(activeCategory).toLowerCase() === String(catId).toLowerCase()) return;
    activeCategory = catId;
    
    const container = document.getElementById('category-container');
    if (!container) return;

    const pills = container.querySelectorAll('.category-pill');
    pills.forEach(pill => {
        const isCurrent = String(pill.dataset.categoryId).toLowerCase() === String(catId).toLowerCase();
        pill.setAttribute('aria-pressed', isCurrent);
        const iconBox = pill.querySelector('div');
        const icon = pill.querySelector('i');
        const text = pill.querySelector('span');
        
        if (iconBox && icon && text) {
            if (isCurrent) {
                iconBox.className = 'w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-200 bg-primary text-white shadow-md shadow-primary/25 border-primary scale-[1.03] border';
                icon.className = icon.className.replace(/text-accent/g, 'text-white');
                text.className = 'text-[11px] text-center w-full truncate transition-colors text-primary font-bold';
            } else {
                iconBox.className = 'w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-200 bg-white text-gray-700 border-gray-200/80 hover:border-primary/40 hover:bg-gray-50/50 border';
                icon.className = icon.className.replace(/text-white/g, 'text-accent');
                text.className = 'text-[11px] text-center w-full truncate transition-colors text-textdark/70 font-medium';
            }
        }
    });

    scrollActiveCategoryIntoView(catId);
}

function handleCategoryClick(catId) {
    if (searchQuery) {
        const searchInput = document.getElementById('search-menu');
        if (searchInput) searchInput.value = '';
        searchQuery = '';
    }

    if (catId === 'all') {
        filterCategory('all');
        const header = document.getElementById('sticky-header');
        const catBar = document.getElementById('sticky-categories');
        const totalOffset = (header ? header.offsetHeight : 0) + (catBar ? catBar.offsetHeight : 0);
        window.scrollTo({ top: Math.max(0, (catBar ? catBar.offsetTop : 0) - totalOffset), behavior: 'smooth' });
        return;
    }

    const section = document.getElementById('cat-section-' + catId);
    if (section && activeCategory === 'all') {
        isProgrammaticScroll = true;
        clearTimeout(scrollTimeout);
        
        setActiveCategoryPill(catId);

        const header = document.getElementById('sticky-header');
        const catBar = document.getElementById('sticky-categories');
        const totalOffset = (header ? header.offsetHeight : 0) + (catBar ? catBar.offsetHeight : 0) + 12;
        const targetTop = section.getBoundingClientRect().top + window.pageYOffset - totalOffset;

        window.scrollTo({
            top: targetTop,
            behavior: 'smooth'
        });

        scrollTimeout = setTimeout(() => {
            isProgrammaticScroll = false;
        }, 800);
    } else {
        filterCategory(catId);
    }
}

function filterCategory(catId) {
    activeCategory = catId;
    renderCategories();
    
    const container = document.getElementById('menu-container');
    if (container) {
        container.classList.remove('shoji-slide');
        void container.offsetWidth; 
        container.classList.add('shoji-slide');
    }

    renderMenu();
}

function initCategoryScrollSpy() {
    if (typeof window === 'undefined') return;

    let ticking = false;
    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (isProgrammaticScroll) {
                    ticking = false;
                    return;
                }
                const sections = document.querySelectorAll('.category-section');
                if (!sections.length) {
                    ticking = false;
                    return;
                }

                const header = document.getElementById('sticky-header');
                const catBar = document.getElementById('sticky-categories');
                const totalOffset = (header ? header.offsetHeight : 0) + (catBar ? catBar.offsetHeight : 0) + 35;

                let currentActiveId = 'all';

                if (window.pageYOffset < 150) {
                    currentActiveId = 'all';
                } else {
                    sections.forEach(sec => {
                        const rect = sec.getBoundingClientRect();
                        if (rect.top <= totalOffset) {
                            currentActiveId = sec.dataset.categoryId;
                        }
                    });
                }

                if (currentActiveId && currentActiveId !== activeCategory) {
                    setActiveCategoryPill(currentActiveId);
                }
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
}

function renderMenu() {
    const container = document.getElementById('menu-container');
    if (!container) return;
    
    // Set layout class
    const theme = typeof SHOP_THEME !== 'undefined' ? SHOP_THEME : 'list';
    if (theme === 'list') {
        container.className = 'p-4 grid grid-cols-1 gap-3.5 relative z-10';
    } else if (theme === 'compact') {
        container.className = 'p-4 grid grid-cols-1 gap-2.5 relative z-10';
    } else if (theme === 'cards') {
        container.className = 'p-4 grid grid-cols-1 gap-5 relative z-10';
    } else if (theme === 'sticker') {
        container.className = 'p-3.5 grid grid-cols-2 gap-3 relative z-10';
    } else if (theme === 'bistro') {
        container.className = 'p-4 grid grid-cols-1 gap-3 relative z-10';
    } else {
        container.className = 'p-4 grid grid-cols-2 gap-3.5 relative z-10';
    }
    
    let filteredMenu = getApiMenu();
    
    const categories = getApiCategories();
    const activeCatObj = categories.find(c => String(c.id).toLowerCase() === String(activeCategory).toLowerCase()) || null;

    if (activeCategory !== 'all') {
        filteredMenu = filteredMenu.filter(m => isItemInCategory(m, activeCatObj || activeCategory));
    }
    if (searchQuery) {
        filteredMenu = filteredMenu.filter(m => m.name.toLowerCase().includes(searchQuery));
    }

    // Dynamic Header Title & "Lihat Semua" Link
    const titleEl = document.getElementById('menu-list-title');
    const viewAllEl = document.getElementById('menu-view-all-btn');
    if (titleEl) {
        if (searchQuery) {
            titleEl.textContent = `Hasil Pencarian "${searchQuery}"`;
            if (viewAllEl) viewAllEl.classList.remove('hidden');
        } else if (activeCategory === 'all') {
            titleEl.textContent = 'Semua Menu';
            if (viewAllEl) viewAllEl.classList.add('hidden');
        } else if (activeCategory === 'promo-diskon') {
            titleEl.textContent = 'Menu Promo';
            if (viewAllEl) viewAllEl.classList.remove('hidden');
        } else {
            const foundCat = categories.find(c => String(c.id).toLowerCase() === String(activeCategory).toLowerCase());
            titleEl.textContent = foundCat ? foundCat.name : 'Daftar Menu';
            if (viewAllEl) viewAllEl.classList.remove('hidden');
        }
    }

    let htmlOutput = '';

    if (activeCategory === 'all' && !searchQuery) {
        // Group by category
        const categorizedItemIds = new Set();
        categories.forEach(cat => {
            if (cat.id === 'all') return;
            const itemsInCat = filteredMenu.filter(m => isItemInCategory(m, cat));
            if (itemsInCat.length > 0) {
                itemsInCat.forEach(m => categorizedItemIds.add(m.id));
                const colSpan = (theme === 'grid' || theme === 'sticker') ? 'col-span-2' : 'col-span-1';
                htmlOutput += `<div id="cat-section-${escapeHtml(cat.id)}" class="${colSpan} category-section mt-5 mb-2 border-b-2 border-primary/10 pb-1.5 scroll-mt-40" data-category-id="${escapeHtml(cat.id)}"><h3 class="font-heading font-bold text-lg text-primary flex items-center gap-2"><i class="fas ${cat.icon} text-sm opacity-70"></i><span>${escapeHtml(cat.name)}</span></h3></div>`;
                htmlOutput += itemsInCat.map(item => renderMenuItem(item)).join('');
            }
        });

        // Any uncategorized items (e.g. whose category was deleted or unassigned)
        const uncategorizedItems = filteredMenu.filter(m => !categorizedItemIds.has(m.id));
        if (uncategorizedItems.length > 0) {
            const colSpan = (theme === 'grid' || theme === 'sticker') ? 'col-span-2' : 'col-span-1';
            htmlOutput += `<div id="cat-section-uncategorized" class="${colSpan} category-section mt-5 mb-2 border-b-2 border-primary/10 pb-1.5 scroll-mt-40" data-category-id="uncategorized"><h3 class="font-heading font-bold text-lg text-primary flex items-center gap-2"><i class="fas fa-layer-group text-sm opacity-70"></i><span>Menu Lainnya</span></h3></div>`;
            htmlOutput += uncategorizedItems.map(item => renderMenuItem(item)).join('');
        }
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

function formatItemPriceHtml(item, textSize = 'text-sm') {
    const isPromo = item.has_active_discount || (Boolean(item.is_discount_active) && item.discount_price > 0 && item.discount_price < item.price);
    const effPrice = isPromo ? Number(item.effective_price || item.discount_price) : Number(item.price);
    const discountSettings = window.SHOP_DISCOUNT_SETTINGS || {};
    const showBadge = discountSettings.show_discount_badge !== false;

    if (isPromo) {
        if (!showBadge) {
            return `<span class="font-bold text-primary ${textSize}">${formatRp(effPrice)}</span>`;
        }
        const pct = item.discount_percentage || Math.round(((item.price - effPrice) / item.price) * 100);
        return `
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="font-bold text-primary ${textSize}">${formatRp(effPrice)}</span>
                <span class="text-[10.5px] text-gray-400 line-through font-normal">${formatRp(item.price)}</span>
                ${pct ? `<span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.2 rounded-full shadow-xs">-${pct}%</span>` : ''}
            </div>
        `;
    }
    return `<span class="font-bold text-primary ${textSize}">${formatRp(item.price)}</span>`;
}

function formatStickerPrice(amount) {
    const num = Number(amount);
    if (isNaN(num)) return amount;
    if (num >= 1000 && num % 1000 === 0) {
        return (num / 1000) + 'k';
    }
    if (num >= 1000) {
        const k = (num / 1000).toFixed(1).replace('.0', '');
        return k + 'k';
    }
    return formatRp(num);
}

function renderMenuItem(item) {
    const theme = typeof SHOP_THEME !== 'undefined' ? SHOP_THEME : 'list';
    const isSoldOut = Boolean(item.soldOut);
    
    if (theme === 'sticker') {
        const isPromo = item.has_active_discount || (Boolean(item.is_discount_active) && item.discount_price > 0 && item.discount_price < item.price);
        const effPrice = isPromo ? Number(item.effective_price || item.discount_price) : Number(item.price);
        const stickerLabel = formatStickerPrice(effPrice);

        return `
            <div class="bg-white rounded-2xl p-3 border border-gray-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition duration-200 relative ${isSoldOut ? 'opacity-65 bg-gray-50/70' : 'cursor-pointer'}" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <div>
                    <div class="flex items-start justify-between gap-1">
                        <h3 class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2">${escapeHtml(item.name)}</h3>
                        ${isSoldOut ? '<span class="text-[7.5px] font-black text-red-600 bg-red-50 border border-red-200 px-1 py-0.2 rounded shrink-0">Habis</span>' : ''}
                    </div>
                    <p class="text-[10.5px] text-gray-500 line-clamp-2 mt-0.5 leading-snug">${escapeHtml(item.desc || '')}</p>
                </div>
                
                <div class="relative my-2.5 mx-auto w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center">
                    <div class="w-full h-full rounded-full overflow-hidden border border-black/10 bg-gray-100 shadow-xs flex items-center justify-center">
                        <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" loading="lazy" class="w-full h-full object-cover ${isSoldOut ? 'grayscale-[50%]' : ''}">
                    </div>
                    <!-- Slanted Yellow Sticker Price Badge -->
                    <div class="absolute -top-1 -right-1 sm:right-0 bg-[#fde047] text-gray-950 font-black text-[11px] sm:text-xs px-2 py-0.5 rounded-md shadow-xs -rotate-6 border border-black/15 z-10 flex items-center gap-1 select-none pointer-events-none">
                        <span>${escapeHtml(stickerLabel)}</span>
                    </div>
                    ${isSoldOut ? '<span class="absolute inset-0 m-auto w-14 h-6 rounded-full bg-red-600/90 text-white text-[9px] font-black flex items-center justify-center shadow-xs uppercase">Habis</span>' : (item.isBestSeller ? '<span class="absolute bottom-0 left-0 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-full shadow-xs">⭐</span>' : '')}
                </div>

                <div class="mt-auto pt-1 flex items-center justify-between">
                    <div class="leading-none">
                        ${formatItemPriceHtml(item, 'text-xs sm:text-sm')}
                    </div>
                    ${isSoldOut ? `
                        <span class="text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded-full">Habis</span>
                    ` : `
                        <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="w-7 h-7 rounded-full border border-gray-800 text-gray-800 hover:bg-gray-900 hover:text-white flex items-center justify-center shadow-2xs active:scale-95 transition-all">
                            <i class="fas fa-plus text-[10px]"></i>
                        </button>
                    `}
                </div>
            </div>
        `;
    }

    if (theme === 'bistro') {
        return `
            <div class="bg-white rounded-2xl p-3 border border-gray-200/90 shadow-xs flex items-center gap-3 sm:gap-4 hover:shadow-md transition duration-200 relative ${isSoldOut ? 'opacity-65 bg-gray-50/70' : 'cursor-pointer'}" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden shrink-0 border border-black/10 bg-gray-100 shadow-xs flex items-center justify-center relative">
                    <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" loading="lazy" class="w-full h-full object-cover ${isSoldOut ? 'grayscale-[50%]' : ''}">
                    ${isSoldOut ? '<span class="absolute inset-0 m-auto w-14 h-5 rounded-full bg-red-600/90 text-white text-[8px] font-black flex items-center justify-center shadow-xs uppercase">Habis</span>' : (item.isBestSeller ? '<span class="absolute top-1 left-1 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-full shadow-xs">⭐</span>' : '')}
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch py-0.5">
                    <div>
                        <div class="flex items-center gap-1.5 pr-6">
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base leading-tight truncate">${escapeHtml(item.name)}</h3>
                            ${isSoldOut ? '<span class="text-[8px] font-black text-red-600 bg-red-50 border border-red-200 px-1 py-0.2 rounded shrink-0">Sold Out</span>' : (item.isBestSeller ? '<span class="text-[8px] font-black text-amber-600 bg-amber-50 border border-amber-200 px-1 py-0.2 rounded shrink-0">Best</span>' : '')}
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-1 leading-snug">${escapeHtml(item.desc || '')}</p>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <div>
                            ${formatItemPriceHtml(item, 'text-sm sm:text-base')}
                        </div>
                        ${isSoldOut ? `
                            <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full">Habis</span>
                        ` : `
                            <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="h-8 w-8 rounded-full border border-gray-800 text-gray-800 hover:bg-gray-900 hover:text-white flex items-center justify-center shadow-xs active:scale-95 transition-all">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;
    }
    
    if (theme === 'compact') {
        return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2.5 flex items-center justify-between gap-3 hover:shadow-md transition duration-200 relative ${isSoldOut ? 'opacity-65 bg-gray-50/70' : 'cursor-pointer'}" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-gray-100 bg-gray-50">
                        <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover ${isSoldOut ? 'grayscale-[50%]' : ''}">
                        ${isSoldOut ? '<span class="absolute top-0.5 left-0.5 bg-red-600 text-white text-[7px] font-black px-1 rounded shadow-sm">Habis</span>' : (item.isBestSeller ? '<span class="absolute top-0.5 left-0.5 bg-amber-500 text-white text-[7px] font-black px-1 rounded shadow-sm">⭐</span>' : '')}
                    </div>
                    <div class="truncate">
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-bold text-xs text-textdark truncate">${escapeHtml(item.name)}</h4>
                            ${isSoldOut ? '<span class="text-[8px] font-bold text-red-600 bg-red-50 px-1 rounded border border-red-200">Sold Out</span>' : (item.isBestSeller ? '<span class="text-[9px] text-amber-600 font-extrabold shrink-0 bg-amber-50 px-1 rounded">Best</span>' : '')}
                        </div>
                        <div class="mt-0.5 block">${formatItemPriceHtml(item, 'text-xs')}</div>
                    </div>
                </div>
                ${isSoldOut ? `
                    <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-lg shrink-0">Habis</span>
                ` : `
                    <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="bg-primary text-white h-7 w-7 rounded-lg flex items-center justify-center shadow-sm active:scale-95 transition-transform shrink-0">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                `}
            </div>
        `;
    }

    if (theme === 'cards') {
        return `
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300 relative ${isSoldOut ? 'opacity-65 bg-gray-50/70' : 'cursor-pointer'}" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <div class="relative w-full h-44 bg-gray-100">
                    <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" loading="lazy" class="w-full h-full object-cover ${isSoldOut ? 'grayscale-[50%]' : ''}">
                    ${isSoldOut ? '<span class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 uppercase tracking-wider"><i class="fas fa-ban text-[9px]"></i> Sold Out</span>' : (item.isBestSeller ? '<span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1"><i class="fas fa-crown text-[9px]"></i> Best Seller</span>' : '')}
                    ${item.categoryId === 'beverages' ? '<div class="steam-effect"></div>' : ''}
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-textdark text-base leading-tight">${escapeHtml(item.name)}</h3>
                            ${isSoldOut ? '<span class="text-[9px] font-black text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded uppercase">Sold Out</span>' : ''}
                        </div>
                        <div class="shrink-0 text-right">${formatItemPriceHtml(item, 'text-base')}</div>
                    </div>
                    ${item.desc ? `<p class="text-xs text-gray-500 line-clamp-2 mt-1 mb-3">${escapeHtml(item.desc)}</p>` : ''}
                    <div class="mt-auto pt-2 flex justify-end">
                        ${isSoldOut ? `
                            <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-xl">Sold Out (Habis)</span>
                        ` : `
                            <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="bg-primary text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md active:scale-95 transition-transform flex items-center gap-1.5">
                                <i class="fas fa-plus text-[10px]"></i> Tambah
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;
    }

    if (theme === 'list') {
        // Horizontal Layout (GoFood style)
        return `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex hover:shadow-md transition duration-300 relative h-28 ${isSoldOut ? 'opacity-65 bg-gray-50/70' : ''}">
                <div class="w-28 flex-shrink-0 relative cursor-pointer" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                    <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" loading="lazy" class="h-full w-full object-cover ${isSoldOut ? 'grayscale-[50%]' : ''}">
                    ${isSoldOut ? '<span class="absolute top-1.5 left-1.5 bg-red-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm">Habis</span>' : (item.isBestSeller ? '<span class="absolute top-1.5 left-1.5 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center gap-0.5"><i class="fas fa-crown text-[7px]"></i> Best</span>' : '')}
                    ${item.categoryId === 'beverages' ? '<div class="steam-effect"></div>' : ''}
                </div>
                <div class="p-3 flex flex-col flex-grow relative cursor-pointer" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                    <div class="flex items-center gap-1.5 mb-1 pr-6">
                        <h3 class="font-bold text-textdark text-sm leading-tight truncate">${escapeHtml(item.name)}</h3>
                        ${isSoldOut ? '<span class="text-[8px] font-black text-red-600 bg-red-50 border border-red-200 px-1 py-0.2 rounded shrink-0">Sold Out</span>' : ''}
                    </div>
                    <p class="text-xs text-gray-500 line-clamp-2 leading-snug">${escapeHtml(item.desc || '')}</p>
                    <div class="mt-auto flex justify-between items-end">
                        ${formatItemPriceHtml(item, 'text-sm')}
                    </div>
                </div>
                ${isSoldOut ? `
                    <span class="absolute bottom-3 right-3 text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full">Habis</span>
                ` : `
                    <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="absolute bottom-3 right-3 bg-primary text-white h-8 w-8 rounded-full flex items-center justify-center shadow-md active:scale-95 transition-transform z-10">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                `}
            </div>
        `;
    }

    // Vertical Layout (Instagram style grid)
    return `
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300 relative ${isSoldOut ? 'opacity-65 bg-gray-50/70' : ''}">
            <div class="p-2 relative cursor-pointer" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <img src="${safeImageUrl(item.image || item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" alt="${escapeHtml(item.name)}" loading="lazy" class="h-32 w-full object-cover rounded-xl ${isSoldOut ? 'grayscale-[50%]' : ''}">
                ${isSoldOut ? '<span class="absolute top-3 left-3 bg-red-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm uppercase tracking-wide">Sold Out</span>' : (item.isBestSeller ? '<span class="absolute top-3 left-3 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center gap-0.5"><i class="fas fa-crown text-[7px]"></i> Best</span>' : '')}
                ${item.categoryId === 'beverages' ? '<div class="steam-effect"></div>' : ''}
            </div>
            <div class="px-3 pb-3 pt-1 flex flex-col flex-grow relative cursor-pointer" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click();}" onclick="${isSoldOut ? `showToast('Menu ini sedang habis (Sold Out)')` : `openItemDetail('${Number(item.id)}')`}">
                <div class="flex items-center gap-1 mb-1">
                    <h3 class="font-bold text-textdark text-xs leading-tight truncate">${escapeHtml(item.name)}</h3>
                    ${isSoldOut ? '<span class="text-[7.5px] font-black text-red-600 bg-red-50 border border-red-200 px-1 rounded shrink-0">Habis</span>' : ''}
                </div>
                <div class="mt-auto flex justify-between items-end">
                    ${formatItemPriceHtml(item, 'text-sm')}
                </div>
            </div>
            ${isSoldOut ? `
                <div class="absolute bottom-2 right-2 bg-red-50 border border-red-200 text-red-600 h-7 px-2 rounded-full flex items-center justify-center text-[9px] font-extrabold cursor-not-allowed">
                    Habis
                </div>
            ` : `
                <button aria-label="Tambah ${escapeHtml(item.name)} ke keranjang" onclick="quickAddToCart('${Number(item.id)}', event)" class="absolute bottom-2 right-2 bg-primary text-white h-7 w-7 rounded-full flex items-center justify-center shadow-md active:scale-95 transition-transform z-10">
                    <i class="fas fa-plus text-[10px]"></i>
                </button>
            `}
        </div>
    `;
}

function quickAddToCart(itemId, event) {
    event.stopPropagation();
    const menuData = getApiMenu();
    currentSelectedItem = menuData.find(m => m.id == itemId);
    if(currentSelectedItem) {
        if (currentSelectedItem.soldOut) {
            showToast(`Menu <b>${escapeHtml(currentSelectedItem.name)}</b> sedang habis (Sold Out).`);
            return;
        }
        confirmAddToCart(); // Adds defaults
    }
}

function updateDetailPrice() {
    if (!currentSelectedItem) return;
    const isPromo = currentSelectedItem.has_active_discount || (Boolean(currentSelectedItem.is_discount_active) && currentSelectedItem.discount_price > 0 && currentSelectedItem.discount_price < currentSelectedItem.price);
    let basePrice = isPromo ? Number(currentSelectedItem.effective_price || currentSelectedItem.discount_price) : Number(currentSelectedItem.price);
    
    let addonTotal = 0;
    const checkedInputs = document.querySelectorAll('#dynamic-addons input:checked');
    checkedInputs.forEach(input => {
        addonTotal += parseInt(input.getAttribute('data-price') || '0');
    });

    const totalPrice = basePrice + addonTotal;
    const priceEl = document.getElementById('detail-price');
    if (priceEl) {
        if (isPromo) {
            priceEl.innerHTML = `
                <div class="flex items-baseline gap-2 flex-wrap">
                    <span class="text-xl font-bold text-primary">${formatRp(totalPrice)}</span>
                    <span class="text-sm text-gray-400 line-through font-normal">${formatRp(Number(currentSelectedItem.price) + addonTotal)}</span>
                </div>
            `;
        } else {
            priceEl.innerText = formatRp(totalPrice);
        }
    }

    const addBtn = document.getElementById('add-cart-btn');
    if (addBtn) {
        addBtn.innerText = `Tambah ke Keranjang — ${formatRp(totalPrice)}`;
    }
}

// Fitur Detail Produk & Customization (Pop-up Add-ons)
function openItemDetail(itemId) {
    const menuData = getApiMenu();
    currentSelectedItem = menuData.find(m => m.id == itemId);
    if (!currentSelectedItem) return;

    if (currentSelectedItem.soldOut) {
        showToast(`Menu <b>${escapeHtml(currentSelectedItem.name)}</b> sedang habis (Sold Out).`);
        return;
    }

    const modGroups = (currentSelectedItem.modifier_groups || currentSelectedItem.modifierGroups || []).filter(g => g.is_active !== false);

    // Jika menu TIDAK memiliki addons, langsung masuk ke keranjang tanpa pop-up
    if (modGroups.length === 0) {
        const isPromoAdd = currentSelectedItem.has_active_discount || (Boolean(currentSelectedItem.is_discount_active) && currentSelectedItem.discount_price > 0 && currentSelectedItem.discount_price < currentSelectedItem.price);
        const effPriceAdd = isPromoAdd ? Number(currentSelectedItem.effective_price || currentSelectedItem.discount_price) : Number(currentSelectedItem.price);
        
        const itemToAdd = {
            ...currentSelectedItem,
            price: effPriceAdd,
            regular_price: Number(currentSelectedItem.price),
            has_discount: isPromoAdd,
            modifier_ids: []
        };

        CartStore.add(itemToAdd, {}, '');
        updateFloatingCart();
        showToast(`Ditambahkan <b>${escapeHtml(currentSelectedItem.name)}</b>`);
        return;
    }

    // Jika menu MEMILIKI addons, tampilkan pop-up / bottom sheet
    document.getElementById('detail-img').src = currentSelectedItem.image || currentSelectedItem.img || '/Assests/null%20image.webp';
    document.getElementById('detail-name').innerText = currentSelectedItem.name;
    document.getElementById('detail-desc').innerText = currentSelectedItem.desc || '';
    
    document.getElementById('detail-notes').value = '';
    
    const addonsContainer = document.getElementById('dynamic-addons');
    if (addonsContainer) {
        let html = '';
        modGroups.forEach(group => {
            const isSingle = (group.selection_type === 'SINGLE' || group.max_selections === 1);
            const modifiers = (group.modifiers || []).filter(m => m.is_active !== false);
            if (modifiers.length === 0) return;

            html += `
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-sm text-textdark font-heading">${escapeHtml(group.name)}</h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${isSingle ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                            ${isSingle ? 'Pilih 1 (Radio)' : 'Bisa Pilih Banyak'}
                        </span>
                    </div>
                    <div class="flex flex-col gap-2">
            `;

            modifiers.forEach((mod, idx) => {
                const inputType = isSingle ? 'radio' : 'checkbox';
                const inputName = isSingle ? `addon_group_${group.id}` : `addon_check_${mod.id}`;
                const isChecked = (isSingle && idx === 0) ? 'checked' : '';
                const priceNum = Number(mod.price_adjustment) || 0;
                const priceText = priceNum > 0 ? `+${formatRp(priceNum)}` : 'Gratis';

                html += `
                    <label class="flex items-center justify-between p-3 border-2 border-primary/10 rounded-xl cursor-pointer hover:border-primary/30 transition bg-white" onchange="updateDetailPrice()">
                        <div class="flex items-center gap-3">
                            <input type="${inputType}" name="${inputName}" data-group-id="${group.id}" data-mod-id="${mod.id}" data-mod-name="${escapeHtml(mod.name)}" data-price="${priceNum}" ${isChecked} class="accent-primary w-4 h-4">
                            <span class="text-sm font-semibold text-textdark">${escapeHtml(mod.name)}</span>
                        </div>
                        <span class="text-xs font-bold text-primary">${priceText}</span>
                    </label>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });
        addonsContainer.innerHTML = html;
    }

    updateDetailPrice();
    document.getElementById('modal-item-detail').classList.remove('hidden');
}

function closeItemDetail() {
    document.getElementById('modal-item-detail').classList.add('hidden');
    currentSelectedItem = null;
}

function confirmAddToCart() {
    if (!currentSelectedItem) return;
    
    if (navigator.vibrate) navigator.vibrate(100);
    const btn = document.getElementById('add-cart-btn');
    if (btn) {
        btn.classList.add('stamp-active');
        setTimeout(() => btn.classList.remove('stamp-active'), 200);
    }

    let notesElement = document.getElementById('detail-notes');
    let customNotes = notesElement ? notesElement.value.trim() : '';
    let additionalPrice = 0;
    let selectedAddonNames = [];
    let selectedModifierIds = [];
    
    const checkedInputs = document.querySelectorAll('#dynamic-addons input:checked');
    checkedInputs.forEach(input => {
        const modName = input.getAttribute('data-mod-name');
        const modId = parseInt(input.getAttribute('data-mod-id'));
        const price = parseInt(input.getAttribute('data-price') || '0');
        
        if (modName) selectedAddonNames.push(modName);
        if (modId) selectedModifierIds.push(modId);
        additionalPrice += price;
    });

    let fullNotes = customNotes;
    if (selectedAddonNames.length > 0) {
        const addonText = selectedAddonNames.join(', ');
        fullNotes = fullNotes ? `${addonText} (${fullNotes})` : addonText;
    }

    const isPromoAdd = currentSelectedItem.has_active_discount || (Boolean(currentSelectedItem.is_discount_active) && currentSelectedItem.discount_price > 0 && currentSelectedItem.discount_price < currentSelectedItem.price);
    const effPriceAdd = isPromoAdd ? Number(currentSelectedItem.effective_price || currentSelectedItem.discount_price) : Number(currentSelectedItem.price);

    const itemToAdd = {
        ...currentSelectedItem,
        price: effPriceAdd + additionalPrice,
        regular_price: Number(currentSelectedItem.price) + additionalPrice,
        has_discount: isPromoAdd,
        modifier_ids: selectedModifierIds
    };

    CartStore.add(itemToAdd, {}, fullNotes);
    
    closeItemDetail();
    updateFloatingCart();
    showToast(`Ditambahkan <b>${escapeHtml(currentSelectedItem.name)}</b>`);
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

function initCartPage() {
    renderCartDetail();
    document.querySelectorAll('input[name="orderType"]').forEach(radio => {
        radio.addEventListener('change', renderCartDetail);
    });
}

let appliedVoucher = null;

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
    const detectedTable = localStorage.getItem((window.SHOP_SLUG || 'default') + '_table_qr');
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
        let notesText = item.notes ? `<p class="text-[10px] mt-1 p-1 bg-bgbase rounded text-primary">📝 ${escapeHtml(item.notes)}</p>` : '';
        return `
        <div class="flex items-center gap-4 bg-white p-3 rounded-xl border-2 border-primary/10 mb-3 relative">
            <img src="${safeImageUrl(item.img)}" onerror="this.onerror=null; this.src='/Assests/null%20image.webp'" loading="lazy" class="w-16 h-16 rounded-lg object-cover">
            <div class="flex-grow">
                <h4 class="font-bold text-sm text-textdark leading-tight font-heading">${escapeHtml(item.name)}</h4>
                <div class="flex items-center gap-1.5 mt-1">
                    <p class="text-primary font-bold text-sm">${formatRp(item.price)}</p>
                    ${item.has_discount ? '<span class="bg-[#fff0e6] text-primary text-[9px] font-bold px-1.5 py-0.5 rounded border border-[#ffd2b8]">PROMO</span>' : ''}
                </div>
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
    const tax = 0;
    const discount = appliedVoucher ? (appliedVoucher.discount_amount || 0) : 0;
    const grandTotal = Math.max(0, subtotal - discount + tax);

    document.getElementById('summary-subtotal').innerText = formatRp(subtotal);
    document.getElementById('summary-tax').innerText = formatRp(tax);

    const discountRow = document.getElementById('summary-discount-row');
    if (discountRow) {
        if (discount > 0 && appliedVoucher) {
            discountRow.classList.remove('hidden');
            const labelEl = document.getElementById('summary-discount-label');
            if (labelEl) labelEl.innerText = `Diskon (${appliedVoucher.code})`;
            const discValEl = document.getElementById('summary-discount');
            if (discValEl) discValEl.innerText = '-' + formatRp(discount);
        } else {
            discountRow.classList.add('hidden');
        }
    }

    document.getElementById('cart-detail-total').innerText = formatRp(grandTotal);
    document.getElementById('btn-pay-text').innerText = 'Lanjut Pembayaran - ' + formatRp(grandTotal);
    
    window.currentGrandTotal = grandTotal;
}

function handleUpdateQty(index, change) { CartStore.updateQty(index, change); renderCartDetail(); }

async function applyPromoCode() {
    const promoInput = document.getElementById('customer-promo');
    const msgEl = document.getElementById('promo-message');
    const btn = document.getElementById('btn-apply-promo');
    const code = promoInput?.value.trim().toUpperCase();

    if (!code) {
        appliedVoucher = null;
        localStorage.removeItem((window.SHOP_SLUG || 'default') + '_customer_promo');
        if (msgEl) {
            msgEl.className = 'text-xs mt-1.5 text-gray-500';
            msgEl.innerText = 'Kode promo dihapus.';
            msgEl.classList.remove('hidden');
        }
        renderCartDetail();
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    try {
        const subtotal = CartStore.getSubtotal();
        const res = await fetch(`/${window.SHOP_SLUG}/voucher/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ code: code, subtotal: subtotal })
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
            appliedVoucher = null;
            localStorage.removeItem((window.SHOP_SLUG || 'default') + '_customer_promo');
            if (msgEl) {
                msgEl.className = 'text-xs mt-1.5 text-red-600 font-semibold';
                msgEl.innerText = data.message || 'Kode promo tidak valid.';
                msgEl.classList.remove('hidden');
            }
            showToast(data.message || '⚠️ Kode promo tidak valid.');
        } else {
            appliedVoucher = {
                code: data.voucher.code,
                name: data.voucher.name,
                discount_amount: Number(data.discount_amount || 0)
            };
            localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_promo', data.voucher.code);
            if (msgEl) {
                msgEl.className = 'text-xs mt-1.5 text-emerald-600 font-bold';
                msgEl.innerText = `✅ Diskon Rp ${Number(data.discount_amount).toLocaleString('id-ID')} berhasil diterapkan!`;
                msgEl.classList.remove('hidden');
            }
            showToast('✅ Kode promo berhasil diterapkan!');
        }
    } catch (e) {
        console.error('applyPromoCode error:', e);
        if (msgEl) {
            msgEl.className = 'text-xs mt-1.5 text-red-600';
            msgEl.innerText = 'Gagal memeriksa kode promo. Periksa koneksi internet Anda.';
            msgEl.classList.remove('hidden');
        }
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Terapkan';
        }
        renderCartDetail();
    }
}

function openCustomerInfoModal() {
    if (CartStore.get().length === 0) return;
    
    // Auto-fill table from QR if exists
    let qrTable = localStorage.getItem((window.SHOP_SLUG || 'default') + '_table_qr');
    const tableInput = document.getElementById('customer-table');
    if (qrTable && tableInput) {
        tableInput.value = qrTable;
        // Kunci input agar tidak bisa diubah oleh pelanggan
        tableInput.readOnly = true;
        tableInput.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
        tableInput.classList.remove('bg-white');
    }

    const savedPromo = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_promo');
    const promoInput = document.getElementById('customer-promo');
    if (promoInput && savedPromo && !promoInput.value) {
        promoInput.value = savedPromo;
    }

    document.getElementById('modal-customer-info').classList.remove('hidden');
}

function closeCustomerInfoModal() {
    document.getElementById('modal-customer-info').classList.add('hidden');
}

function selectOrderType(type) {
    const isTakeaway = (type === 'Takeaway');
    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) orderTypeInput.value = isTakeaway ? 'Takeaway' : 'Dine-in';

    const btnDineIn = document.getElementById('btn-type-dine-in');
    const btnTakeaway = document.getElementById('btn-type-takeaway');
    const tableContainer = document.getElementById('table-input-container');
    const takeawayInfo = document.getElementById('takeaway-info-container');

    if (btnDineIn && btnTakeaway) {
        if (isTakeaway) {
            btnTakeaway.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-bold text-xs transition-all duration-200 bg-white text-gray-900 shadow-sm border border-gray-200/80';
            btnTakeaway.querySelector('i')?.classList.add('text-primary');
            btnDineIn.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-bold text-xs transition-all duration-200 text-gray-500 hover:text-gray-800';
            btnDineIn.querySelector('i')?.classList.remove('text-primary');
        } else {
            btnDineIn.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-bold text-xs transition-all duration-200 bg-white text-gray-900 shadow-sm border border-gray-200/80';
            btnDineIn.querySelector('i')?.classList.add('text-primary');
            btnTakeaway.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-bold text-xs transition-all duration-200 text-gray-500 hover:text-gray-800';
            btnTakeaway.querySelector('i')?.classList.remove('text-primary');
        }
    }

    if (tableContainer) {
        if (isTakeaway) {
            tableContainer.classList.add('hidden');
        } else {
            tableContainer.classList.remove('hidden');
        }
    }

    if (takeawayInfo) {
        if (isTakeaway) {
            takeawayInfo.classList.remove('hidden');
        } else {
            takeawayInfo.classList.add('hidden');
        }
    }
}

function triggerPaymentGateway() {
    const orderType = document.getElementById('order-type')?.value || 'Dine-in';
    const isTakeaway = (orderType.toLowerCase() === 'takeaway');
    const fulfillmentType = isTakeaway ? 'TAKEAWAY' : 'DINE_IN';

    const name = document.getElementById('customer-name')?.value.trim();
    const email = document.getElementById('customer-email')?.value.trim();
    const phone = document.getElementById('customer-phone')?.value.trim();
    
    let table = '';
    if (!isTakeaway) {
        table = localStorage.getItem((window.SHOP_SLUG || 'default') + '_table_qr');
        if (!table) {
            table = document.getElementById('customer-table')?.value.trim();
        }
        if (!table) {
            showToast("⚠️ Mohon isi / scan Nomor Meja untuk Dine In!"); 
            return; 
        }
    }
    
    if(!name) { 
        showToast("⚠️ Mohon isi Nama Anda!"); 
        return; 
    }
    
    if(email && (email.length < 5 || !email.includes('@'))) {
        showToast("⚠️ Format email tidak valid!");
        return;
    }

    if(phone && phone.length < 9) {
        showToast("⚠️ Format nomor WhatsApp tidak valid!");
        return;
    }

    localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_name', name);
    localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_table', table);
    localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_email', email);
    localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_phone', phone);
    localStorage.setItem((window.SHOP_SLUG || 'default') + '_fulfillment_type', fulfillmentType);

    const promo = document.getElementById('customer-promo')?.value.trim().toUpperCase() || '';
    if (promo) {
        localStorage.setItem((window.SHOP_SLUG || 'default') + '_customer_promo', promo);
    } else {
        localStorage.removeItem((window.SHOP_SLUG || 'default') + '_customer_promo');
        appliedVoucher = null;
    }
    
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
    const name = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_name') || 'Guest';
    const email = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_email') || '';
    const phone = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_phone') || '';
    const table = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_table') || '';
    const fulfillmentType = localStorage.getItem((window.SHOP_SLUG || 'default') + '_fulfillment_type') || 'DINE_IN';
    const promo = localStorage.getItem((window.SHOP_SLUG || 'default') + '_customer_promo') || '';
    const items = CartStore.get();
    const total = window.currentGrandTotal;
    
    // Create order using database.js engine
    DB.createOrder(table, name, email, phone, paymentMethod, items, total, fulfillmentType, promo)
        .then(data => {
            localStorage.setItem((window.SHOP_SLUG || 'default') + '_last_order_type', fulfillmentType === 'TAKEAWAY' ? 'Take Away' : 'Dine In');
            localStorage.setItem((window.SHOP_SLUG || 'default') + '_last_order_total', data.order.grand_total);
            CartStore.clear(); 
            window.location.href = window.SHOP_TRACKING_URL;
        })
        .catch(err => {
            alert('Gagal membuat pesanan. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = `Bayar Sekarang`;
        });
}

function initTrackingPage() {
    document.getElementById('track-type').innerText = localStorage.getItem((window.SHOP_SLUG || 'default') + '_last_order_type') || 'Dine-in';
    document.getElementById('track-total').innerText = formatRp(Number(localStorage.getItem((window.SHOP_SLUG || 'default') + '_last_order_total') || '0'));
    setTimeout(() => { const modal = document.getElementById('modal-rating'); if(modal) modal.classList.remove('hidden'); }, 3000);
}

function sanitizeToastHtml(html) {
    if (!html) return '';
    let safe = escapeHtml(html);
    return safe.replace(/&lt;b&gt;/gi, '<b class="font-bold text-white">')
               .replace(/&lt;\/b&gt;/gi, '</b>')
               .replace(/&lt;strong&gt;/gi, '<strong class="font-bold text-white">')
               .replace(/&lt;\/strong&gt;/gi, '</strong>');
}

function showToast(message) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    // Bersihkan toast aktif sebelumnya agar tidak menumpuk
    container.innerHTML = '';

    const toast = document.createElement('div');
    toast.className = 'toast-msg bg-textdark text-white text-xs sm:text-sm px-4 py-2.5 rounded-full shadow-xl flex items-center gap-2.5 font-medium mx-auto w-max max-w-[92vw] pointer-events-auto border border-white/10';

    const isWarning = typeof message === 'string' && (message.includes('⚠️') || message.toLowerCase().includes('mohon') || message.toLowerCase().includes('tidak valid'));
    const iconClass = isWarning ? 'fa-exclamation-circle text-amber-400' : 'fa-check-circle text-primary';

    const cleanMessage = typeof message === 'string' ? message.replace(/^[⚠️✅]\s*/, '') : '';

    toast.innerHTML = `<i class="fas ${iconClass} text-sm shrink-0"></i> <span class="truncate">${sanitizeToastHtml(cleanMessage)}</span>`;
    container.appendChild(toast);

    clearTimeout(window.__toastTimeout);
    window.__toastTimeout = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(() => toast.remove(), 250);
    }, 2200);
}

function closeRating() { document.getElementById('modal-rating').classList.add('hidden'); }

function goBackHome(event) {
    if (event) event.preventDefault();
    let qrTable = localStorage.getItem((window.SHOP_SLUG || 'default') + '_table_qr');
    if (qrTable) {
        window.location.href = window.SHOP_HOME_URL + '?table=' + qrTable;
    } else {
        window.location.href = window.SHOP_HOME_URL;
    }
}
function setRating(stars) { /* Same implementation as previous */ }

function initApp() {
    // 1. Deteksi Meja dari URL (QR Code)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('table')) {
        localStorage.setItem((window.SHOP_SLUG || 'default') + '_table_qr', urlParams.get('table'));
    }

    const page = document.body ? document.body.dataset.page : null;
    if (page === 'home') initHomePage();
    if (page === 'cart') initCartPage();
    if (page === 'tracking') initTrackingPage();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

