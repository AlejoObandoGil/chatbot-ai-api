<?php

namespace Database\Seeders;

use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Chatbot::create([
            'user_id' => 1,
            'name' => 'TelcoBot',
            'description' => 'Chatbot para la empresa de telefonía, televisión y internet.'
        ]);
    }
}
