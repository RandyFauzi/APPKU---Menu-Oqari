<?php

namespace App\Console\Commands;

use App\Services\SchemaCompatibility;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpgradeLegacyDatabase extends Command
{
    protected $signature = 'app:upgrade-legacy-database {--force : Apply the verified legacy upgrade}';

    protected $description = 'Reconcile a pre-existing variant column, migrate, and check database compatibility';

    public function handle(Migrator $migrator): int
    {
        if (! $this->option('force')) {
            $this->error('Use --force to apply the legacy database upgrade.');

            return self::FAILURE;
        }
        $name = '2026_10_16_030932_add_variant_id_to_order_items_table';
        $repository = $migrator->getRepository();
        if ($migrator->repositoryExists() && ! in_array($name, $repository->getRan(), true) && Schema::hasColumn('order_items', 'variant_id')) {
            if (! Schema::hasTable('product_variants')) {
                $this->error('product_variants is missing; the existing variant_id cannot be verified.');

                return self::FAILURE;
            }
            $invalid = DB::table('order_items')->whereNotNull('variant_id')->whereNotExists(function ($query): void {
                $query->selectRaw('1')->from('product_variants')->whereColumn('product_variants.id', 'order_items.variant_id');
            })->exists();
            if ($invalid) {
                $this->error('Existing variant references are invalid. No order history was changed; reconcile those references first.');

                return self::FAILURE;
            }
            $foreignKeys = Schema::getForeignKeys('order_items');
            $variantKeys = array_filter($foreignKeys, fn (array $key): bool => $key['columns'] === ['variant_id']);
            foreach ($variantKeys as $key) {
                if ($key['foreign_table'] !== 'product_variants' || $key['foreign_columns'] !== ['id']) {
                    $this->error('variant_id has an unexpected foreign key; refusing to change it.');

                    return self::FAILURE;
                }
            }
            $column = collect(Schema::getColumns('order_items'))->firstWhere('name', 'variant_id');
            if (! str_contains($column['type_name'], 'int')) {
                $this->error('variant_id is not an integer column; refusing to change it.');

                return self::FAILURE;
            }
            if ($variantKeys === [] || ! $column['nullable']) {
                Schema::table('order_items', fn (Blueprint $table) => $table->unsignedBigInteger('variant_id')->nullable()->change());
            }
            if ($variantKeys === []) {
                Schema::table('order_items', fn (Blueprint $table) => $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete());
            }
            $repository->log($name, $repository->getNextBatchNumber());
            $this->info('Verified existing variant_id and reconciled its migration history.');
        }
        $result = $this->call('migrate', ['--force' => true]);
        if ($result !== self::SUCCESS) {
            return $result;
        }
        $this->laravel->forgetInstance(SchemaCompatibility::class);

        return $this->call('app:check-database-schema');
    }
}
