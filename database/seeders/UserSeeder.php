<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /*public function run(): void
    {
        $users = [
            ['name' => 'tanveer', 'email' => 'tanveer@example.com', 'role_id' => 1, 'password' => bcrypt('password')],
            ['name' => 'rai', 'email' => 'rai@example.com', 'role_id' => 2, 'password' => bcrypt('password')],
            ['name' => 'jatinder', 'email' => 'jatinder@example.com', 'role_id' => 3, 'password' => bcrypt('password')],
            ['name' => 'umair', 'email' => 'umair@example.com', 'role_id' => 4, 'password' => bcrypt('password')],
            ['name' => 'rashid', 'email' => 'rashid@example.com', 'role_id' => 5, 'password' => bcrypt('password')],
            ['name' => 'faisal', 'email' => 'faisal@example.com', 'role_id' => 6, 'password' => bcrypt('password')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }*/
        public function run(): void
    {
        $domain = 'resisqaure.co.uk';

        $users = [
            [
                'name'  => 'rai',
                'email' => "rai@{$domain}",
                'role'  => 'Landlord',
            ],
            [
                'name'  => 'tanveer',
                'email' => "tanveer@{$domain}",
                'role'  => 'Super Admin',
            ],
            [
                'name'  => 'Jatinder',
                'email' => "Jatinder@{$domain}",
                'role'  => 'Property Manager',
            ],
            [
                'name'  => 'umair',
                'email' => "umair@{$domain}",
                'role'  => 'Staff',
            ],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name'              => $u['name'],
                    'email_verified_at' => now(),
                    'password'          => Hash::make('password'), // change to secure default
                    'remember_token'    => Str::random(10),
                ]
            );

            // Assign the role (will attach if not already)
            $user->assignRole($u['role']);
        }
    }
}
