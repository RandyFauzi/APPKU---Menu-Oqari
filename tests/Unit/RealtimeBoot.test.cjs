const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const vm = require('node:vm');
const source = readFileSync(join(__dirname, '../../resources/js/echo.js'), 'utf8')
    .replace(/^import .*;$/gm, '').replaceAll('import.meta.env', 'env');
function boot(env) {
    const configurations = [];
    const context = vm.createContext({env, window: {}, Pusher: class {}, Echo: class { constructor(options) { configurations.push(options); } }});
    vm.runInContext(source, context);
    return {context, configurations};
}

test('missing realtime configuration does not stop application initialization', () => {
    const {context, configurations} = boot({});
    assert.equal(configurations.length, 0);
    assert.equal(context.window.Echo, undefined);
});

test('configured realtime client keeps private channel transport configuration', () => {
    const {context, configurations} = boot({VITE_PUSHER_APP_KEY: 'test-public-key', VITE_PUSHER_APP_CLUSTER: 'ap1'});
    assert.equal(configurations.length, 1);
    assert.equal(configurations[0].key, 'test-public-key');
    assert.equal(configurations[0].broadcaster, 'pusher');
    assert.equal(configurations[0].forceTLS, true);
    assert.ok(context.window.Echo);
});
