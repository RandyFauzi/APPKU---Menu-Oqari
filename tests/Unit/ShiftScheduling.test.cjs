const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');

function app() {
    const source = fs.readFileSync('resources/views/Admin/Dashboard/dashboard.blade.php', 'utf8');
    const state = source.slice(source.indexOf('                shiftsLoading: false,'), source.indexOf('                async fetchLogs('));
    const methods = source.slice(source.indexOf('                async saveShift()'), source.indexOf('                addTableFromModal()'));
    const component = new Function('return ({' + (state + methods).replace(/\{\{[\s\S]*?\}\}/g, '{}') + '});')();
    Object.assign(component, { can: () => true, shifts: [], shiftDate: '2026-11-04', addToast() {}, $refs: { shiftDialog: { close() {}, showModal() {} } } });
    return component;
}

test('loads the selected period and employees and clears loading state', async () => {
    const component = app();
    global.fetch = async url => {
        assert.match(url, /date=2026-11-04/);
        return { ok: true, json: async () => ({ data: [{id:1,user_name:'Kasir',position:'Pagi'}], employees: [{id:1}], timezone: 'Asia/Makassar' }) };
    };
    await component.fetchShifts();
    assert.equal(component.shifts.length, 1);
    assert.equal(component.shiftEmployees.length, 1);
    assert.equal(component.shiftsLoading, false);
    component.shiftSearch = 'KASIR';
    assert.equal(component.filteredShifts.length, 1);
});

test('shows a load failure instead of an empty schedule', async () => {
    const component = app();
    global.fetch = async () => ({ ok: false });
    await component.fetchShifts();
    assert.ok(component.shiftsError);
    assert.equal(component.shiftsLoading, false);
});

test('preserves the form and exposes server validation errors', async () => {
    const component = app();
    let closed = false;
    component.$refs.shiftDialog.close = () => { closed = true; };
    global.fetch = async () => ({ ok: false, json: async () => ({ errors: {start_time:['Jadwal bertabrakan.']} }) });
    await component.saveShift();
    assert.equal(component.shiftFormError, 'Jadwal bertabrakan.');
    assert.equal(component.shiftSaving, false);
    assert.equal(closed, false);
});

test('successful save selects the saved date and refreshes the list', async () => {
    const component = app();
    let closed = false;
    let refreshed = false;
    component.newShift = { id: 4, date: '2026-11-12' };
    component.$refs.shiftDialog.close = () => { closed = true; };
    component.fetchShifts = async () => { refreshed = true; };
    global.fetch = async (_, request) => {
        assert.equal(JSON.parse(request.body).id, 4);
        return { ok: true, json: async () => ({success:true}) };
    };
    await component.saveShift();
    assert.equal(component.shiftDate, '2026-11-12');
    assert.equal(component.shiftSaving, false);
    assert.equal(closed, true);
    assert.equal(refreshed, true);
});
