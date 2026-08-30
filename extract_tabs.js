
const fs = require('fs');
const content = fs.readFileSync('resources/views/Admin/Dashboard/dashboard.blade.php', 'utf8');

function extractTab(tabName, outputFileName) {
    const startRegex = new RegExp('<div x-show=.currentTab === \\'' + tabName + '\\'..+?');
    const matchStart = startRegex.exec(content);
    if (!matchStart) {
        console.log('Tab ' + tabName + ' not found');
        return;
    }
    const startIndex = matchStart.index;
    
    let count = 0;
    let endIndex = startIndex;
    const divRegex = /<\/?div[^>]*>/gi;
    divRegex.lastIndex = startIndex;
    let match;
    while ((match = divRegex.exec(content)) !== null) {
        if (match[0].startsWith('</div')) {
            count--;
        } else if (match[0].startsWith('<div')) {
            count++;
        }
        
        if (count === 0) {
            endIndex = match.index + match[0].length;
            break;
        }
    }
    
    const tabContent = content.substring(startIndex, endIndex);
    fs.writeFileSync('resources/views/Admin/Dashboard/tabs/' + outputFileName, tabContent);
    console.log('Extracted ' + tabName + ' to ' + outputFileName);
}

extractTab('orders', 'orders.blade.php');
extractTab('analytics', 'analytics.blade.php');
extractTab('qr', 'qr.blade.php');
extractTab('settings', 'settings.blade.php');

