<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:fix-migrations')]
#[Description('Command description')]
class FixMigrations extends Command
{
    protected $signature = 'app:fix-migrations';
    protected $description = 'Fix missing migration records in the database by checking if tables exist';

    public function handle()
    {
        $this->info('Fixing migrations table...');
        
        $files = \Illuminate\Support\Facades\File::files(database_path('migrations'));
        
        $migrationRecords = \Illuminate\Support\Facades\DB::table('migrations')->pluck('migration')->toArray();
        $batch = \Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0;
        $batch++;

        $count = 0;
        foreach ($files as $file) {
            $filename = str_replace('.php', '', $file->getFilename());
            
            // Skip if already in migrations table
            if (in_array($filename, $migrationRecords)) {
                continue;
            }

            // Guess table name from filename
            // e.g. 2026_08_31_041527_create_shift_templates_table -> shift_templates
            if (preg_match('/_create_(.*)_table/', $filename, $matches)) {
                $tableName = $matches[1];
                
                if (\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
                    \Illuminate\Support\Facades\DB::table('migrations')->insert([
                        'migration' => $filename,
                        'batch' => $batch
                    ]);
                    $this->info("Inserted $filename into migrations table (Table $tableName exists).");
                    $count++;
                }
            } 
        }

        $this->info("Done! Inserted $count missing migration records.");
    }
}
