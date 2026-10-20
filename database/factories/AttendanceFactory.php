<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Attendance> */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'user_id' => fn (array $attributes) => User::factory()->create(['shop_id' => $attributes['shop_id']])->id,
            'employee_name' => fn (array $attributes) => User::findOrFail($attributes['user_id'])->name,
            'work_date' => now('Asia/Jakarta')->toDateString(), 'timezone' => 'Asia/Jakarta',
            'checked_in_at' => now('UTC'),
        ];
    }
}
