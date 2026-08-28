import re, glob

files = glob.glob('database/migrations/*_add_role_to_users_table.php')
if not files:
    print('Migration not found')
    exit()

with open(files[0], 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    'public function up(): void\n    {\n        Schema::table(\'users\', function (Blueprint ) {\n            //\n        });\n    }',
    'public function up(): void\n    {\n        Schema::table(\'users\', function (Blueprint ) {\n            ->string(\'role\')->default(\'admin\');\n        });\n    }'
).replace(
    'public function down(): void\n    {\n        Schema::table(\'users\', function (Blueprint ) {\n            //\n        });\n    }',
    'public function down(): void\n    {\n        Schema::table(\'users\', function (Blueprint ) {\n            ->dropColumn(\'role\');\n        });\n    }'
)

with open(files[0], 'w', encoding='utf-8') as f:
    f.write(content)
print('Migration updated')
