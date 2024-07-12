<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Intent\IntentResponse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IntentResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IntentResponse::create([
            'intent_id' => 1,
            'response' => 'Nuestro plan de Internet ofrece alta velocidad y precios competitivos.'
        ]);

        IntentResponse::create([
            'intent_id' => 1,
            'response' => 'Ofrecemos planes de Internet, Teléfono y TV. ¿Cuál te interesa?'
        ]);

        IntentResponse::create([
            'intent_id' => 2,
            'response' => 'Por favor escriba su numero de documento.'
        ]);

        IntentResponse::create([
            'intent_id' => 2,
            'response' => 'El saldo registrado es {{Obtener saldo del cliente}}.'
        ]);

        IntentResponse::create([
            'intent_id' => 3,
            'response' => 'Lamentamos escuchar eso. ¿Cuál es el problema que estás experimentando?'
        ]);

        IntentResponse::create([
            'intent_id' => 3,
            'response' => '¿Puedes proporcionar más detalles sobre el problema?'
        ]);

        IntentResponse::create([
            'intent_id' => 4,
            'response' => '¿Qué tipo de servicio necesitas?'
        ]);

        IntentResponse::create([
            'intent_id' => 4,
            'response' => 'Entendido. Vamos a proceder con tu solicitud.'
        ]);
    }
}
