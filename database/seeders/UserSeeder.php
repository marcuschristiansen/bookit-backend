<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Marcus Christiansen',
            'email' => env('SUPER_ADMIN_EMAIL'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('super-admin');

        User::factory(10)->create()->each(function($user) {
            $user->assignRole('company-admin');
        });

        User::factory(30)->create()->each(function($user) {
            $user->assignRole('user');
        });
    }
}
