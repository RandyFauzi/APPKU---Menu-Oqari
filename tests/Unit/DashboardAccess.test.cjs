const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function dashboard(permissions) {
    const source = fs.readFileSync('resources/views/Admin/Dashboard/dashboard.blade.php', 'utf8');
    const start = source.indexOf("document.addEventListener('alpine:init'");
    const end = source.indexOf('</script>', start);
    let factory;
    const context = {
        window: { OQARI_THEME: { primaryOptions: [], primary: '#641d35' }, INITIAL_DATA: { permissions, user: {}, categories: [], shop: {} }, location: { search: '' }, innerWidth: 1200 },
        document: { addEventListener: (event, callback) => callback() },
        Alpine: { data: (name, callback) => { factory = callback; } },
        localStorage: { getItem: () => null }, URLSearchParams,
    };
    vm.runInNewContext(source.slice(start, end).replace(/\{\{[\s\S]*?\}\}/g, '{}'), context);
    return factory();
}

test('inventory-only user sees inventory navigation without other modules', () => {
    const app = dashboard(['manage-inventory']);
    assert.deepEqual(Array.from(app.tabs, tab => tab.id), ['inventory']);
    assert.equal(app.can('manage-users'), false);
});

test('restricted dashboard skips calls to forbidden APIs', async () => {
    const app = dashboard(['manage-inventory']);
    await app.loadMenu();
    await app.initAnalytics();
    await app.fetchLiveOrders();
    await app.fetchShifts();
    await app.fetchLogs();
    assert.equal(app.isLoadingMenus, false);
    assert.equal(app.isLoadingAnalytics, false);
});
