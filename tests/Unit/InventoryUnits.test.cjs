const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');
const source = readFileSync(join(__dirname, '../../resources/views/Admin/Dashboard/dashboard.blade.php'), 'utf8');
function method(name) {
    const start = source.indexOf(name + '(');
    let position = source.indexOf('{', start), depth = 0;
    for (; position < source.length; position++) {
        if (source[position] === '{') depth++;
        if (source[position] === '}' && --depth === 0) return source.slice(start, position + 1);
    }
    throw new Error('Method not found: ' + name);
}
function app() {
    const requests = [];
    const context = vm.createContext({ fetch(url, options) { requests.push({url, options}); return new Promise(() => {}); } });
    const state = vm.runInContext('({' + ['formatStock', 'stockUnitLabel', 'submitStockAdjustment'].map(method).join(',') + '})', context);
    state.stockUnits = {pcs:'Pcs', dus:'Dus', botol:'Botol', pack:'Pack'};
    state.addToast = () => {};
    state.adjustForm = {raw_material_id: 1, type: 'adjustment', amount: '0'};
    return {state, requests};
}

test('stock balances retain six decimal places when needed and packaging labels are consistent', () => {
    const {state} = app();
    assert.equal(state.formatStock('10.000001'), '10,000001');
    assert.equal(state.formatStock('10.000000'), '10');
    for (const [unit, label] of Object.entries(state.stockUnits)) assert.equal(state.stockUnitLabel(unit), label);
    assert.equal(state.stockUnitLabel('karung'), 'karung');
});

test('zero stock opname is submitted but zero restock and waste are blocked', () => {
    const {state, requests} = app();
    state.submitStockAdjustment();
    assert.equal(requests.length, 1);
    for (const type of ['in', 'waste']) {
        state.adjustForm.type = type;
        state.submitStockAdjustment();
    }
    assert.equal(requests.length, 1);
});

test('empty, negative, and nonnumeric adjustments do not send requests', () => {
    const {state, requests} = app();
    for (const amount of ['', -1, 'invalid']) { state.adjustForm.amount = amount; state.submitStockAdjustment(); }
    assert.equal(requests.length, 0);
});
