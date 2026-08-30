import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace incomingOrder state properties
state_pattern = r"incomingOrder: null,"
new_state = """incomingOrderQueue: [],
                activeIncomingOrder: null,
                incomingOrderState: 'idle',
                incomingTimer: 45,
                incomingTimerInterval: null,
                formatTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                },"""
content = re.sub(state_pattern, new_state, content)

# Replace fetchLiveOrders to handle queues and images
fetch_pattern = r"fetchLiveOrders\(isInit = false\) \{.*?this\.orders = dbOrders;\s*\}"
new_fetch = """fetchLiveOrders(isInit = false) {
                    const dbOrders = window.INITIAL_DATA.orders.map(o => ({
                        id: o.id,
                        customer: o.customer_name || ('Meja ' + (o.table ? o.table.name : '-')),
                        status: o.status,
                        total: o.total_price,
                        time: o.created_at,
                        items: o.items.map(i => ({ 
                            name: i.product.name, 
                            qty: i.quantity, 
                            price: i.price, 
                            image: i.product.image_url ? ('/storage/' + i.product.image_url) : null,
                            desc: i.product.description
                        }))
                    }));
                    
                    const newIncomingOrders = dbOrders.filter(o => o.status === 'Masuk' && !this.orders.find(old => old.id === o.id));
                    
                    if (!isInit && newIncomingOrders.length > 0) {
                        const chime = document.getElementById('chime-sound');
                        if (chime) {
                            chime.currentTime = 0;
                            chime.play().catch(e => console.log('Audio autoplay blocked', e));
                        }
                        
                        newIncomingOrders.forEach(o => this.incomingOrderQueue.push(o));
                        this.processIncomingQueue();
                    }
                    
                    this.orders = dbOrders;
                }"""
content = re.sub(fetch_pattern, new_fetch, content, flags=re.DOTALL)

# Add queue processing methods (replacing old acceptIncomingOrder)
accept_pattern = r"acceptIncomingOrder\(\) \{.*?\}(?=\s*,\s*fetchLiveOrders)"
new_methods = """processIncomingQueue() {
                    if (!this.activeIncomingOrder && this.incomingOrderQueue.length > 0) {
                        this.activeIncomingOrder = this.incomingOrderQueue.shift();
                        this.incomingOrderState = 'new';
                        this.incomingTimer = 45;
                        clearInterval(this.incomingTimerInterval);
                        
                        this.incomingTimerInterval = setInterval(() => {
                            if (['new', 'reject_confirm'].includes(this.incomingOrderState)) {
                                this.incomingTimer--;
                                if (this.incomingTimer <= 0) {
                                    this.confirmRejectOrder(true);
                                }
                            }
                        }, 1000);
                    }
                },
                acceptOrder() {
                    this.incomingOrderState = 'accepting';
                    setTimeout(() => {
                        this.incomingOrderState = 'accepted';
                        this.updateStatus(this.activeIncomingOrder.id, 'In Progress');
                        setTimeout(() => this.closeActiveOrder(), 1500);
                    }, 800);
                },
                confirmRejectOrder(isAuto = false) {
                    this.incomingOrderState = 'rejected';
                    this.updateStatus(this.activeIncomingOrder.id, 'Dibatalkan');
                    if (isAuto) {
                        this.addToast(`Pesanan #${this.activeIncomingOrder.id} telah kedaluwarsa.`, 'error');
                    }
                    setTimeout(() => this.closeActiveOrder(), 1500);
                },
                closeActiveOrder() {
                    this.activeIncomingOrder = null;
                    clearInterval(this.incomingTimerInterval);
                    setTimeout(() => this.processIncomingQueue(), 400);
                }"""
content = re.sub(accept_pattern, new_methods, content, flags=re.DOTALL)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated JS Logic for Incoming Order Queue")
