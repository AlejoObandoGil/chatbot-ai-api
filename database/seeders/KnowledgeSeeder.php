<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chatbot\Knowledge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KnowledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Knowledge::create([
            'chatbot_id' => 1,
            'topic' => 'Planes de Internet',
            'content' => 'Ofrecemos planes de internet de alta velocidad para hogar y oficina.'
        ]);

        Knowledge::create([
            'chatbot_id' => 1,
            'topic' => 'Planes de Telefonía',
            'content' => 'Nuestros planes de telefonía incluyen llamadas ilimitadas y roaming internacional.'
        ]);

        Knowledge::create([
            'chatbot_id' => 1,
            'topic' => 'Planes de TV',
            'content' => 'Ofrecemos una variedad de canales de entretenimiento, deportes y noticias.'
        ]);

        Knowledge::create([
            'chatbot_id' => 1,
            'topic' => 'Soporte Técnico',
            'content' => 'Nuestro equipo de soporte técnico está disponible 24/7 para resolver cualquier problema.'
        ]);
    }
}
