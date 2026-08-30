import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Find the second init()
matches = list(re.finditer(r'init\(\) \{', content))
if len(matches) >= 2:
    # Get the block of the first init to see if there's anything else we need. 
    # Actually, let's just insert into the second init.
    
    second_init_pos = matches[-1].end()
    
    code_to_insert = '''
                    this.('currentTab', (val) => {
                        localStorage.setItem('activeDashboardTab', val);
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });
'''
    
    new_content = content[:second_init_pos] + code_to_insert + content[second_init_pos:]
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f2:
        f2.write(new_content)
    print('Successfully updated init()')
else:
    print('Could not find second init')
