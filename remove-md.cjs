const fs = require('fs');
['menu', 'cart', 'tracking'].forEach(f => {
    let p = `resources/views/shop/${f}.blade.php`;
    let c = fs.readFileSync(p, 'utf8');
    c = c.replace(/\smd:[^\s"'>]+/g, '');
    fs.writeFileSync(p, c);
});
console.log('done');
