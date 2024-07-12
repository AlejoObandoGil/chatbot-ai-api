<?php

namespace Database\Seeders;

use App\Models\Entity\Entity;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entity::create([
            'chatbot_id' => 1,
            'name' => 'Tipo de plan',
            'type' => 'string',
            'save' => true
        ]);

        Entity::create([
            'chatbot_id' => 1,
            'name' => 'Saldo de cliente',
            'type' => 'string',
            'save' => true
        ]);

        Entity::create([
            'chatbot_id' => 1,
            'name' => 'Tipo de servicio',
            'type' => 'string',
            'save' => true
        ]);

        Entity::create([
            'chatbot_id' => 1,
            'name' => 'Tipo de problema',
            'type' => 'string',
            'save' => true
        ]);
    }
}
