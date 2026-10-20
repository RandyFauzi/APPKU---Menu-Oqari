<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $shop = Shop::firstOrCreate(['slug' => 'attendance-demo'], ['name' => 'Cafe Demo Absensi', 'status' => 'active']);
        $employee = User::firstOrCreate(['email' => 'attendance@example.test'], [
            'name' => 'Karyawan Demo', 'shop_id' => $shop->id, 'role' => 'crew', 'password' => Hash::make('DemoAttendance123!'),
        ]);
        Attendance::firstOrCreate([
            'shop_id' => $shop->id, 'user_id' => $employee->id, 'work_date' => now('Asia/Jakarta')->subDay()->toDateString(),
        ], [
            'employee_name' => $employee->name,
            'timezone' => 'Asia/Jakarta', 'checked_in_at' => now('UTC')->subDay()->startOfHour(),
            'checked_out_at' => now('UTC')->subDay()->startOfHour()->addHours(8), 'worked_minutes' => 480,
        ]);
    }
}
