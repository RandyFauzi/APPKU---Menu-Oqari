const fs = require('fs');
['menu', 'cart', 'tracking'].forEach(f => {
    let p = `resources/views/shop/${f}.blade.php`;
    if (!fs.existsSync(p)) return;
    let c = fs.readFileSync(p, 'utf8');
    
    c = c.replace(/class="([^"]*)fixed inset-0([^"]*)"/g, (match, p1, p2) => {
        if (!match.includes('max-w-[420px]')) {
            return `class="${p1}fixed inset-0 max-w-[420px] mx-auto${p2}"`;
        }
        return match;
    });

    c = c.replace(/class="([^"]*)fixed bottom-0([^"]*)"/g, (match, p1, p2) => {
        let inner = match.replace(/max-w-md/g, 'max-w-[420px]');
        return inner;
    });

    fs.writeFileSync(p, c);
});
console.log('Done fixing modals');
