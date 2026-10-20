const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const css = readFileSync(join(__dirname, '../../resources/css/theme.css'), 'utf8');
const tokens = Object.fromEntries(Array.from(css.matchAll(/(--oq-[a-z0-9-]+):\s*([^;]+);/g), match => [match[1], match[2].trim()]));
function color(token) {
    const value = tokens[token];
    const reference = /var\((--oq-[a-z0-9-]+)\)/.exec(value);
    if (reference) return color(reference[1]);
    if (value.startsWith('#')) return [1,3,5].map(start => parseInt(value.slice(start, start + 2), 16));
    return value.split(/\s+/).map(Number);
}
function luminance(rgb) {
    const channels = rgb.map(value => value / 255).map(value => value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4);
    return channels[0] * .2126 + channels[1] * .7152 + channels[2] * .0722;
}
function contrast(a,b) { const x = luminance(a), y = luminance(b); return (Math.max(x,y)+.05)/(Math.min(x,y)+.05); }

test('body text, muted text, gold labels, and primary buttons meet normal text contrast', () => {
    for (const [foreground, background] of [['--oq-ink','--oq-ivory'],['--oq-muted','--oq-ivory'],['--oq-gold-ink','--oq-ivory'],['--oq-surface','--oq-primary']]) {
        assert.ok(contrast(color(foreground), color(background)) >= 4.5, foreground + ' on ' + background);
    }
});

test('every cafe accent preset supports readable ivory button text', () => {
    const php = readFileSync(join(__dirname, '../../config/theme.php'), 'utf8');
    const presets = new Set(php.match(/#[a-f0-9]{6}/g));
    for (const preset of presets) {
        const rgb = [1,3,5].map(start => parseInt(preset.slice(start,start+2),16));
        assert.ok(contrast(color('--oq-surface'),rgb) >= 4.5, preset);
    }
});
