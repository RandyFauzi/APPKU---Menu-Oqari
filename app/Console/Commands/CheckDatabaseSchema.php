<?php

namespace App\Console\Commands;

use App\Services\SchemaCompatibility;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

class CheckDatabaseSchema extends Command
{
    protected $signature = 'app:check-database-schema {--json : Output structured schema diagnostics}';

    protected $description = 'Check feature tables and columns without modifying the database';

    public function handle(SchemaCompatibility $schema, Migrator $migrator): int
    {
        $missing = $schema->missing();
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        $ran = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];
        $pending = array_values(array_diff($files, $ran));
        $compatible = $missing === [] && $pending === [];
        if ($this->option('json')) {
            $this->line(json_encode(['compatible' => $compatible, 'missing' => $missing, 'pending_migrations' => $pending], JSON_THROW_ON_ERROR));
        } else {
            foreach ($missing as $table => $columns) {
                $this->error($table.': '.implode(', ', $columns));
            }
            foreach ($pending as $migration) {
                $this->warn('Pending: '.$migration);
            }
            $compatible ? $this->info('Database schema compatible.') : $this->warn('Run php artisan migrate --force, then repeat this check.');
        }

        return $compatible ? self::SUCCESS : self::FAILURE;
    }
}
