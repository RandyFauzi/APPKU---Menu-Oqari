<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@oqari.com')->first();

        if (! $admin) {
            User::create([
                'name' => 'Oqari Owner',
                'email' => 'admin@oqari.com',
                'password' => Hash::make('OqariSuper2026!'),
                'role' => 'superadmin',
                'shop_id' => null,
            ]);
        }
    }
}
