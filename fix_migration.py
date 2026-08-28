import re

with open('database/migrations/2026_08_28_104623_add_role_to_users_table.php', 'r', encoding='utf-8') as f:
    content = f.read()

up_pattern = r"public function up\(\): void\s*\{\s*Schema::table\('users', function \(Blueprint \$table\) \{\s*//\s*\}\);\s*\}"
up_repl = """public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin');
        });
    }"""

down_pattern = r"public function down\(\): void\s*\{\s*Schema::table\('users', function \(Blueprint \$table\) \{\s*//\s*\}\);\s*\}"
down_repl = """public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }"""

content = re.sub(up_pattern, up_repl, content)
content = re.sub(down_pattern, down_repl, content)

with open('database/migrations/2026_08_28_104623_add_role_to_users_table.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated migration file")
