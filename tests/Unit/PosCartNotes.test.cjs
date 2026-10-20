const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');

const blade = readFileSync(join(__dirname, '../../resources/views/Admin/pos/index.blade.php'), 'utf8');
const source = blade.slice(blade.indexOf('function posApp()'), blade.indexOf('</script>', blade.indexOf('function posApp()')))
    .replace(/{{[\s\S]*?}}/g, 'null')
    .replace(/^(\s*\w+:)\s*@json.*$/gm, '$1 [],');

function app() {
    const context = vm.createContext({ document: { getElementById: () => ({focus() {}}) } });
    vm.runInContext(source, context);
    const state = context.posApp();
    state.$nextTick = callback => callback();
    return state;
}

const product = { id: 1, name: 'Latte', price: 25000 };

test('ordinary products can receive a note without variants and keep separate from new plain items', () => {
    const state = app();
    state.handleProductClick(product);
    state.cart[0].notes = 'Tanpa gula';
    state.handleProductClick(product);
    assert.equal(state.cart.length, 2);
    assert.equal(state.cart[0].notes, 'Tanpa gula');
    assert.equal(state.cart[1].notes, '');
});

test('splitting one item preserves quantity, totals, variant, and independent notes and modifiers', () => {
    const state = app();
    state.cart = [{ ...product, qty: 3, subtotal: 75000, variant_id: 4, notes: 'Tanpa gula', modifiers: [{id: 9, name: 'Shot'}] }];
    state.splitItem(0);
    assert.equal(state.cart.length, 2);
    assert.equal(state.cart[0].qty, 2);
    assert.equal(state.cart[1].qty, 1);
    assert.equal(state.subtotal, 75000);
    assert.equal(state.cart[1].variant_id, 4);
    state.cart[1].notes = 'Es sedikit';
    state.cart[1].modifiers[0].name = 'Different';
    assert.equal(state.cart[0].notes, 'Tanpa gula');
    assert.equal(state.cart[0].modifiers[0].name, 'Shot');
    const payload = state.cartPayload();
    assert.equal(payload[0].notes, 'Tanpa gula');
    assert.equal(payload[1].notes, 'Es sedikit');
});

test('split respects single quantity, pending payment, and maximum cart size', () => {
    const state = app();
    state.cart = [{...product, qty: 1, subtotal: 25000}];
    state.splitItem(0);
    assert.equal(state.cart.length, 1);
    state.cart[0].qty = 2;
    state.paymentPending = true;
    state.splitItem(0);
    assert.equal(state.cart[0].qty, 2);
    state.paymentPending = false;
    state.cart = Array.from({length: 50}, () => ({...product, qty: 2}));
    state.splitItem(0);
    assert.equal(state.cart.length, 50);
    assert.equal(state.cart[0].qty, 2);
});

test('payload trims notes and clearing a note sends null', () => {
    const state = app();
    state.cart = [{...product, qty: 1, notes: '  Panas\nTanpa gula  ', modifiers: []}];
    assert.equal(state.cartPayload()[0].notes, 'Panas\nTanpa gula');
    state.cart[0].notes = '   ';
    assert.equal(state.cartPayload()[0].notes, null);
});
