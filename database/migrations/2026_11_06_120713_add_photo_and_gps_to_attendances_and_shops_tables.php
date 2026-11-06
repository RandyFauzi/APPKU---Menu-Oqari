<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('attendance_radius_meters')->default(100)->after('longitude');
            $table->boolean('attendance_require_photo')->default(false)->after('attendance_radius_meters');
            $table->boolean('attendance_require_gps')->default(false)->after('attendance_require_photo');
            $table->boolean('attendance_strict_location')->default(false)->after('attendance_require_gps');
        });

        Schema::table('attendances', function (Blueprint $table): void {
            $table->string('check_in_photo_path', 255)->nullable()->after('check_in_note');
            $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in_photo_path');
            $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            $table->float('check_in_accuracy')->nullable()->after('check_in_longitude');
            $table->unsignedInteger('check_in_distance_meters')->nullable()->after('check_in_accuracy');
            $table->boolean('check_in_in_radius')->nullable()->after('check_in_distance_meters');

            $table->string('check_out_photo_path', 255)->nullable()->after('check_out_note');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_photo_path');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            $table->float('check_out_accuracy')->nullable()->after('check_out_longitude');
            $table->unsignedInteger('check_out_distance_meters')->nullable()->after('check_out_accuracy');
            $table->boolean('check_out_in_radius')->nullable()->after('check_out_distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropColumn([
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
            ]);
        });

        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn([
                'latitude',
                'longitude',
                'attendance_radius_meters',
                'attendance_require_photo',
                'attendance_require_gps',
                'attendance_strict_location',
            ]);
        });
    }
};
