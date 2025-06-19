<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'status' => 1, 
            'email_verified_at' => now(),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //    User::create([
        //     'name' => 'admin',
        //     'email' =>  random_int(1, 100) . '@gmail.com',
        //     'password' => bcrypt('admin'),
        //     'role' => 'admin',
        //     'status' => 1, 
        //     'email_verified_at' => now(),
        //     'remember_token' => null,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
        //  User::factory()->count(100)->create();
    }
}
