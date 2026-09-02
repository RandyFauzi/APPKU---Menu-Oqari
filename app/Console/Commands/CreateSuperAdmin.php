<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oqari:create-superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a secure Super Admin account for Oqari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Creating Oqari Super Admin Account");

        $email = $this->ask('Enter super admin email');
        
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email format.');
            return Command::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email {$email} already exists!");
            if (!$this->confirm('Do you want to elevate this existing user to superadmin?')) {
                return Command::FAILURE;
            }
            $user = User::where('email', $email)->first();
            $user->update(['role' => 'superadmin', 'shop_id' => null]);
            $this->info('User successfully elevated to Super Admin.');
            return Command::SUCCESS;
        }

        $name = $this->ask('Enter name', 'Oqari Owner');
        $password = $this->secret('Enter password (min 8 characters)');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return Command::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'superadmin',
            'shop_id' => null,
        ]);

        $this->info("Super Admin account successfully created for {$email}!");
        return Command::SUCCESS;
    }
}
