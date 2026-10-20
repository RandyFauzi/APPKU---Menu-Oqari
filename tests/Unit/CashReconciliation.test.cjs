const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');

const blade = readFileSync(join(__dirname, '../../resources/views/Admin/Register/index.blade.php'), 'utf8');
const source = blade.slice(blade.indexOf('function cashReconciliation('), blade.indexOf('</script>', blade.indexOf('function cashReconciliation(')));
function preview(expected, actual) {
    const context = vm.createContext({Intl});
    vm.runInContext(source, context);
    return context.cashReconciliation(expected, actual);
}

test('blank and invalid counts are not shown as balanced', () => {
    for (const value of ['', null, -1, 'invalid', Infinity]) {
        const state = preview(100, value);
        assert.equal(state.difference, null);
        assert.equal(state.label, 'Belum dihitung');
    }
});

test('cash status shows balanced, short and over with signed differences', () => {
    assert.equal(preview(100, 100).label, 'Kas sesuai');
    assert.equal(preview(100, 90).label, 'Kas kurang');
    assert.equal(preview(100, 90).difference, -10);
    assert.equal(preview(100, 110).label, 'Kas lebih');
    assert.equal(preview(100, 110).difference, 10);
});

test('decimal comparisons match cent precision and zero is a valid physical count', () => {
    assert.equal(preview(100.30, '100.30').difference, 0);
    assert.equal(preview(100.30, '100.29').difference, -0.01);
    assert.equal(preview(100.30, '100.31').difference, 0.01);
    assert.equal(preview(0, '0').label, 'Kas sesuai');
    assert.equal(preview(100, '0').difference, -100);
});
