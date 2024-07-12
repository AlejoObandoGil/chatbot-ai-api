<?php

namespace Database\Seeders;

use App\Models\Intent\Intent;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IntentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Intent::create([
            'chatbot_id' => 1,
            'name' => 'Obtener tipo de plan del cliente',
        ]);

        Intent::create([
            'chatbot_id' => 1,
            'name' => 'Obtener saldo del cliente',
        ]);

        Intent::create([
            'chatbot_id' => 1,
            'name' => 'Reportar un Problema',
        ]);

        Intent::create([
            'chatbot_id' => 1,
            'name' => 'Solicitar un Service',
        ]);
    }
}
