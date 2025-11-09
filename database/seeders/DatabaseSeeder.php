<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Service::factory(10)->create();
        User::factory()->create([
            'name' => 'Admin Account',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), 
            'is_admin' => true,
        ]);
    }
}
