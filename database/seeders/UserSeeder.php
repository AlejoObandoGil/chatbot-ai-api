<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => bcrypt('Password1234.'),
        ]);

        // User::factory(10)->create();
    }
}
