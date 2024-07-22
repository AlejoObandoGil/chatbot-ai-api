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
            'password' => '$2y$12$Jgx0/wlCv9XmrGxjxMNOk.GfiRJUQ4NirhRwWe7JbPqjHtqf3jFeK',
        ]);

        // User::factory(10)->create();
    }
}
