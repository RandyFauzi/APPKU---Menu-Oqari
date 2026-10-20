const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');

const source = readFileSync(join(__dirname, '../../public/js/main.js'), 'utf8');

function app(elements = {}) {
    const context = vm.createContext({
        URL,
        URLSearchParams,
        window: { location: { origin: 'https://menu.example', search: '' } },
        document: { readyState: 'loading', addEventListener() {}, getElementById: id => elements[id] },
    });
    vm.runInContext(source, context);
    return context;
}

test('product rendering treats stored names and descriptions as text', () => {
    const context = app();
    const html = context.renderMenuItem({
        id: 1, name: '<img src=x onerror=alert(1)>', desc: '<script>alert(2)</script>',
        img: 'javascript:alert(3)', price: 30000,
    });
    assert.ok(html.includes('&lt;img src=x onerror=alert(1)&gt;'));
    assert.ok(html.includes('&lt;script&gt;alert(2)&lt;/script&gt;'));
    assert.ok(!html.includes('<script>'));
    assert.ok(!html.includes('src="javascript:'));
});

test('cart notes cannot create HTML and totals do not add an unconfigured tax', () => {
    const elements = {};
    const element = id => elements[id] ??= {
        innerHTML: '', innerText: '', disabled: false,
        classList: { add() {}, remove() {} },
    };
    for (const id of ['cart-items-container', 'btn-trigger-pay', 'summary-section', 'summary-subtotal',
        'summary-tax', 'cart-detail-total', 'btn-pay-text', 'manual-table-input']) element(id);
    const context = app(elements);
    context.CartStore = {
        get: () => [{ id: 1, name: 'Coffee', img: '/coffee.webp', price: 30000, qty: 2, notes: '<svg onload=alert(1)>' }],
        getSubtotal: () => 60000,
    };
    context.localStorage = { getItem: () => null };
    context.renderCartDetail();
    assert.ok(elements['cart-items-container'].innerHTML.includes('&lt;svg onload=alert(1)&gt;'));
    assert.ok(!elements['cart-items-container'].innerHTML.includes('<svg'));
    assert.equal(context.window.currentGrandTotal, 60000);
    assert.equal(elements['summary-tax'].innerText, 'Rp 0');
});

test('category navigation uses a keyboard-operable button and escapes category content', () => {
    const container = { innerHTML: '' };
    const context = app({ 'category-container': container });
    context.apiData = { categories: [{ id: 'all', name: '<svg onload=alert(1)>', icon: 'fa-star' }] };
    context.renderCategories();
    assert.ok(container.innerHTML.includes('<button type="button"'));
    assert.ok(container.innerHTML.includes('aria-pressed="true"'));
    assert.ok(container.innerHTML.includes('&lt;svg onload=alert(1)&gt;'));
    assert.ok(!container.innerHTML.includes('<svg'));
});
