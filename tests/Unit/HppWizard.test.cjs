const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');
const blade = readFileSync(join(__dirname, '../../resources/views/Admin/Hpp/index.blade.php'), 'utf8');
const start = blade.indexOf('function hppCalculator()');
const source = blade.slice(start, blade.indexOf('</script>', start))
    .replace(/{{ Illuminate\\Support\\Js::from\(\$initial\) }}/g, 'initialData')
    .replace(/{{ Illuminate\\Support\\Js::from\(\$materials\) }}/g, 'catalog')
    .replace(/{{[\s\S]*?}}/g, 'null');
function wizard() {
    const initialData = {recipes: [{raw_material_id: '', amount: 1, unit: '', notes: ''}], production_quantity: 1, production_unit: 'porsi', target_margin: '', target_markup: ''};
    const catalog = [{id: 1, name: 'Milk', unit: 'ml', cost_per_unit: '28.500000'}, {id: 2, name: 'Coffee', unit: 'gram', cost_per_unit: '150.000000'}, {id: 3, name: 'Cup', unit: 'pcs', cost_per_unit: '1000.000000'}];
    const context = vm.createContext({initialData, catalog, Intl});
    vm.runInContext(source, context);
    const state = context.hppCalculator();
    state.init();
    return state;
}

test('purchase preview keeps decimal unit cost instead of truncating it', () => {
    const state = wizard();
    state.newMaterial.purchase_total = 28500;
    state.newMaterial.purchase_quantity = 1000;
    assert.equal(state.purchaseUnitCost, 28.5);
    assert.ok(state.unitMoney(state.purchaseUnitCost).includes('28,50'));
    state.newMaterial.purchase_quantity = 0;
    assert.equal(state.purchaseUnitCost, null);
});

test('only compatible recipe units are offered and search keeps selected material', () => {
    const state = wizard();
    const row = state.form.recipes[0];
    row.raw_material_id = 1;
    assert.equal(JSON.stringify(state.recipeUnits(row)), JSON.stringify(['ml', 'liter']));
    state.materialSearch = 'Coffee';
    assert.equal(state.filteredMaterials(row).length, 2);
    row.raw_material_id = 3;
    assert.equal(JSON.stringify(state.recipeUnits(row)), JSON.stringify(['pcs']));
});

test('recipe keys stay unique and editing costs invalidates stale result', () => {
    const state = wizard();
    state.addRow();
    state.removeRow(0);
    state.addRow();
    assert.notEqual(state.form.recipes[0].key, state.form.recipes[1].key);
    state.result = {hpp: 100};
    state.saved = 'Saved';
    state.invalidate();
    assert.equal(state.result, null);
    assert.equal(state.saved, '');
    assert.equal(state.payload().recipes[0].key, undefined);
    assert.equal(state.payload().target_margin, null);
});

test('wizard cannot skip unvisited stages and calculation failure prevents advancing', async () => {
    const state = wizard();
    state.goTo(6);
    assert.equal(state.step, 1);
    state.step = 4;
    state.furthest = 4;
    state.validateStep = () => true;
    state.calculate = async () => false;
    await state.next();
    assert.equal(state.step, 4);
    state.calculate = async () => true;
    await state.next();
    assert.equal(state.step, 5);
    assert.equal(state.furthest, 5);
});
