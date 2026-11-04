const test = require('node:test');
const assert = require('node:assert/strict');

async function sidebar(saved, item = { top: 120, bottom: 164 }) {
    const { sidebarScroll } = await import('../../resources/js/sidebar-scroll.js');
    const storage = new Map(saved === null ? [] : [['cafe-sidebar-scroll:7', saved]]);
    global.sessionStorage = { getItem: key => storage.get(key) ?? null, setItem: (key, value) => storage.set(key, value) };
    let callback;
    let disconnected = false;
    global.MutationObserver = class {
        constructor(listener) { callback = listener; }
        observe() {}
        disconnect() { disconnected = true; }
    };
    const app = sidebarScroll('7');
    app.$el = {
        scrollTop: 0,
        querySelector: () => item ? { getBoundingClientRect: () => item } : null,
        getBoundingClientRect: () => ({ top: 100, bottom: 600 }),
    };
    app.$nextTick = fn => fn();
    app.init();
    return { app, storage, changeActive: callback, disconnected: () => disconnected };
}

test('restores prior scroll position without moving an already visible active item', async () => {
    const { app } = await sidebar('320');
    assert.equal(app.$el.scrollTop, 320);
});

test('reveals a selected item below the sidebar viewport after navigation', async () => {
    const item = { top: 120, bottom: 164 };
    const { app, changeActive, storage } = await sidebar('320', item);
    item.top = 610;
    item.bottom = 654;
    changeActive();
    assert.equal(app.$el.scrollTop, 382);
    assert.equal(storage.get('cafe-sidebar-scroll:7'), '382');
});

test('reveals an item above the viewport using only the sidebar scroll', async () => {
    const { app } = await sidebar('320', { top: 40, bottom: 84 });
    assert.equal(app.$el.scrollTop, 252);
});

test('records manual scrolling and disconnects observer on teardown', async () => {
    const { app, storage, disconnected } = await sidebar(null);
    app.$el.scrollTop = 450;
    app.savePosition();
    assert.equal(storage.get('cafe-sidebar-scroll:7'), '450');
    app.destroy();
    assert.equal(disconnected(), true);
});

test('works when storage is blocked and no active link exists', async () => {
    const { app } = await sidebar('invalid', null);
    global.sessionStorage = { getItem() { throw new Error('blocked'); }, setItem() { throw new Error('blocked'); } };
    assert.doesNotThrow(() => app.init());
    assert.doesNotThrow(() => app.savePosition());
    assert.equal(app.$el.scrollTop, 0);
});
