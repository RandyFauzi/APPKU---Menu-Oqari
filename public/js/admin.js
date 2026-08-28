// C:\laragon\www\Menu Apps\Bitten Coffee\js\admin.js

const formatRp = (angka) => 'Rp ' + angka.toLocaleString('id-ID');

// Listen to local storage changes to simulate real-time socket
window.addEventListener('storage', (e) => {
    if (e.key === 'bitten_orders' || !e.key) {
        loadOrders();
        playChime();
        showAdminToast('Pesanan Baru Masuk!');
    }
});

let orders = [];

function loadOrders() {
    const data = localStorage.getItem('bitten_orders');
    orders = data ? JSON.parse(data) : [];
    renderBoard();
}

function saveOrders() {
    localStorage.setItem('bitten_orders', JSON.stringify(orders));
    renderBoard();
}

function clearAllOrders() {
    if(confirm('Hapus semua pesanan demo?')) {
        localStorage.removeItem('bitten_orders');
        orders = [];
        renderBoard();
    }
}

function renderBoard() {
    const cols = {
        'Masuk': document.getElementById('col-masuk'),
        'Diproses': document.getElementById('col-proses'),
        'Siap': document.getElementById('col-siap'),
        'Selesai': document.getElementById('col-selesai')
    };

    // Clear cols
    Object.values(cols).forEach(col => col.innerHTML = '');

    const counts = { 'Masuk': 0, 'Diproses': 0, 'Siap': 0, 'Selesai': 0 };

    orders.forEach((order, index) => {
        if (!cols[order.status]) order.status = 'Masuk';
        counts[order.status]++;

        const card = document.createElement('div');
        card.className = 'bg-white p-4 rounded border-2 border-gray-100 shadow-sm flex flex-col gap-2 relative';
        
        let typeBadge = order.type === 'Dine-in' 
            ? `<span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded">Meja: ${order.table}</span>`
            : `<span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded">Takeaway</span>`;

        let itemsHtml = order.items.map(item => `
            <div class="flex justify-between text-xs border-b border-gray-50 pb-1 mb-1">
                <span class="font-bold text-gray-700">${item.qty}x ${item.name}</span>
                <span class="text-gray-500">${formatRp(item.price * item.qty)}</span>
            </div>
            ${item.notes ? `<p class="text-[9px] text-gray-400 italic mb-1">Catatan: ${item.notes}</p>` : ''}
        `).join('');

        let actionBtn = '';
        if (order.status === 'Masuk') {
            actionBtn = `<button onclick="updateStatus(${index}, 'Diproses')" class="w-full bg-orange-500 text-white text-xs font-bold py-2 rounded mt-2 hover:bg-orange-600 transition">Proses Pesanan</button>`;
        } else if (order.status === 'Diproses') {
            actionBtn = `<button onclick="updateStatus(${index}, 'Siap')" class="w-full bg-green-500 text-white text-xs font-bold py-2 rounded mt-2 hover:bg-green-600 transition">Pesanan Siap</button>`;
        } else if (order.status === 'Siap') {
            actionBtn = `<button onclick="updateStatus(${index}, 'Selesai')" class="w-full bg-gray-800 text-white text-xs font-bold py-2 rounded mt-2 hover:bg-gray-900 transition">Selesaikan</button>`;
        }

        card.innerHTML = `
            <div class="flex justify-between items-start mb-1">
                <span class="font-bold text-primary font-heading">${order.id}</span>
                <span class="text-[10px] text-gray-400 font-bold">${order.time}</span>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-circle text-gray-300"></i>
                <span class="text-xs font-bold text-gray-600">${order.customer}</span>
                ${typeBadge}
            </div>
            <div class="bg-gray-50 p-2 rounded mb-1">
                ${itemsHtml}
            </div>
            <div class="flex justify-between items-center mt-1">
                <span class="text-[10px] font-bold text-gray-400">Total:</span>
                <span class="text-sm font-bold text-primary">${formatRp(order.total)}</span>
            </div>
            ${actionBtn}
        `;

        cols[order.status].appendChild(card);
    });

    document.getElementById('count-masuk').innerText = counts['Masuk'];
    document.getElementById('count-proses').innerText = counts['Diproses'];
    document.getElementById('count-siap').innerText = counts['Siap'];
    document.getElementById('count-selesai').innerText = counts['Selesai'];

    const badge = document.getElementById('nav-badge');
    if (counts['Masuk'] > 0) {
        badge.innerText = counts['Masuk'];
        badge.classList.remove('hidden');
        badge.classList.add('badge-blink');
    } else {
        badge.classList.add('hidden');
        badge.classList.remove('badge-blink');
    }
}

function updateStatus(index, newStatus) {
    orders[index].status = newStatus;
    saveOrders();
}

function playChime() {
    const audio = document.getElementById('chime-sound');
    if(audio) {
        audio.currentTime = 0;
        audio.play().catch(e => console.log('Audio autoplay prevented by browser.'));
    }
}

function showAdminToast(message) {
    const container = document.getElementById('admin-toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast-enter bg-red-500 text-white px-6 py-4 rounded shadow-lg border-2 border-red-700 flex items-center gap-3 font-bold';
    toast.innerHTML = `<i class="fas fa-bell animate-pulse"></i> <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Initial load
document.addEventListener('DOMContentLoaded', loadOrders);
