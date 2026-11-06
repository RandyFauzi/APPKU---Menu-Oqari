<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            if (! Schema::hasColumn('shops', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('address');
            }
            if (! Schema::hasColumn('shops', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (! Schema::hasColumn('shops', 'attendance_radius_meters')) {
                $table->unsignedInteger('attendance_radius_meters')->default(100)->after('longitude');
            }
            if (! Schema::hasColumn('shops', 'attendance_require_photo')) {
                $table->boolean('attendance_require_photo')->default(false)->after('attendance_radius_meters');
            }
            if (! Schema::hasColumn('shops', 'attendance_require_gps')) {
                $table->boolean('attendance_require_gps')->default(false)->after('attendance_require_photo');
            }
            if (! Schema::hasColumn('shops', 'attendance_strict_location')) {
                $table->boolean('attendance_strict_location')->default(false)->after('attendance_require_gps');
            }
        });

        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table): void {
                if (! Schema::hasColumn('attendances', 'check_in_photo_path')) {
                    $table->string('check_in_photo_path', 255)->nullable()->after('check_in_note');
                }
                if (! Schema::hasColumn('attendances', 'check_in_latitude')) {
                    $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in_photo_path');
                }
                if (! Schema::hasColumn('attendances', 'check_in_longitude')) {
                    $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
                }
                if (! Schema::hasColumn('attendances', 'check_in_accuracy')) {
                    $table->float('check_in_accuracy')->nullable()->after('check_in_longitude');
                }
                if (! Schema::hasColumn('attendances', 'check_in_distance_meters')) {
                    $table->unsignedInteger('check_in_distance_meters')->nullable()->after('check_in_accuracy');
                }
                if (! Schema::hasColumn('attendances', 'check_in_in_radius')) {
                    $table->boolean('check_in_in_radius')->nullable()->after('check_in_distance_meters');
                }

                if (! Schema::hasColumn('attendances', 'check_out_photo_path')) {
                    $table->string('check_out_photo_path', 255)->nullable()->after('check_out_note');
                }
                if (! Schema::hasColumn('attendances', 'check_out_latitude')) {
                    $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_photo_path');
                }
                if (! Schema::hasColumn('attendances', 'check_out_longitude')) {
                    $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
                }
                if (! Schema::hasColumn('attendances', 'check_out_accuracy')) {
                    $table->float('check_out_accuracy')->nullable()->after('check_out_longitude');
                }
                if (! Schema::hasColumn('attendances', 'check_out_distance_meters')) {
                    $table->unsignedInteger('check_out_distance_meters')->nullable()->after('check_out_accuracy');
                }
                if (! Schema::hasColumn('attendances', 'check_out_in_radius')) {
                    $table->boolean('check_out_in_radius')->nullable()->after('check_out_distance_meters');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table): void {
                $columns = array_filter([
                    'check_in_photo_path',
                    'check_in_latitude',
                    'check_in_longitude',
                    'check_in_accuracy',
                    'check_in_distance_meters',
                    'check_in_in_radius',
                    'check_out_photo_path',
                    'check_out_latitude',
                    'check_out_longitude',
                    'check_out_accuracy',
                    'check_out_distance_meters',
                    'check_out_in_radius',
                ], fn ($col) => Schema::hasColumn('attendances', $col));

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('shops')) {
            Schema::table('shops', function (Blueprint $table): void {
                $columns = array_filter([
                    'latitude',
                    'longitude',
                    'attendance_radius_meters',
                    'attendance_require_photo',
                    'attendance_require_gps',
                    'attendance_strict_location',
                ], fn ($col) => Schema::hasColumn('shops', $col));

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
