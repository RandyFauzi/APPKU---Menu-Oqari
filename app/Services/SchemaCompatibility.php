<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SchemaCompatibility
{
    /** @var array<string, list<string>> */
    private array $columns = [];

    /** @return list<string> */
    public function columns(string $table): array
    {
        return $this->columns[$table] ??= Schema::getColumnListing($table);
    }

    /**
     * @param  list<string>|null  $features
     * @return array<string, list<string>>
     */
    public function missing(?array $features = null): array
    {
        $groups = config('database_schema.groups');
        $selected = $features === null ? array_keys($groups) : [];
        foreach ($features ?? [] as $feature) {
            $selected = array_merge($selected, config('database_schema.features.'.$feature, []));
        }
        $required = [];
        foreach (array_unique($selected) as $group) {
            foreach ($groups[$group] as $table => $columns) {
                $required[$table] = array_unique(array_merge($required[$table] ?? [], $columns));
            }
        }
        if ($features === null) {
            foreach (glob(app_path('Models/*.php')) as $file) {
                $class = 'App\\Models\\'.basename($file, '.php');
                $model = new $class;
                $table = $model->getTable();
                $required[$table] = array_unique(array_merge($required[$table] ?? [], $model->getFillable()));
            }
        }
        $missing = [];
        foreach ($required as $table => $columns) {
            $absent = array_values(array_diff($columns, $this->columns($table)));
            if ($absent !== []) {
                $missing[$table] = $absent;
            }
        }

        return $missing;
    }

    /** @param list<string> $features */
    public function requireFeatures(array $features): void
    {
        $missing = $this->missing($features);
        if ($missing !== []) {
            Log::warning('Database feature schema requires migration', ['features' => $features, 'missing' => $missing]);
            abort(503, 'Database perlu diperbarui sebelum fitur ini digunakan. Administrator perlu menjalankan php artisan migrate --force. Data belum diubah.');
        }
    }
}
