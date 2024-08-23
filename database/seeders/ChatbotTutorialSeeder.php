<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Seeder;
use App\Models\Intent\IntentOption;
use App\Models\Intent\IntentResponse;
use App\Models\Intent\IntentTrainingPhrase;

class ChatbotTutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatbotData = [
            'id' => '65b941fb-2d44-476b-9939-fa5723505f2d',
            'user_id' => 1,
            'name' => 'Chatbot Tutorial IA',
            'description' => 'Eres un chatbot encargado de brindar asistencia a nuestros usuarios sobre la plataforma de creación de chatbots y sus usos en la plataforma.',
            'type' => 'Híbrido',
            'assistant_openai_id' => 'asst_AJSJStUDr8TYpsxzG528Wc37',
            'temperature' => 0.5,
            'max_tokens' => 300
        ];

        $chatbot = Chatbot::create($chatbotData);

        $chatbot->config()->create([
            'chatbot_id' => $chatbot->id,
            'chat_color' => '#7986CB',
            'message_color' => '#5C6BC0',
        ]);

        $intentsData = [
            [
                'id' => Str::uuid(),
                'name' => 'Saludo inicial',
                'is_choice' => true,
                'type' => 'customNode',
                'category' => 'saludo',
                'position' => [
                    'x' => 0,
                    'y' => 100,
                ],
                'data' => ['label' => 'Saludo inicial'],
                'phrases' => [
                    'Hola',
                    'Buenos días',
                    'Buenas tardes',
                    'Buenas noches',
                    'Hello',
                    'Hi',
                    'Preguntar sobre otro tema',
                    'Quiero saber más sobre otro tema',
                    'Otro tema de interés',
                    'Menu principal',
                    'Menu',
                    'Menu de inicio',
                    'Regresar al menu',
                ],
                'responses' => [
                    '¡Hola soy tu chatbot guia implementado con el modelo gpt4.0 de openAi! Si necesitas ayuda sobre la creación de chatbots y sus usos en la plataforma puedes escribirla o seleccionar una opción.',
                ],
                'options' => [
                    'Como crear un chatbot con IA?',
                    'Como crear un nodo?',
                    'Como crear un nodo de opción multiple?',
                    'Como crear nodo para guardar información del usuario?',
                    'Como crear el flujo de mi chatbot?',
                    'Como personalizar mi chatbot?',
                    'Como ver mi chatbot en vivo?',
                ]
            ],
        ];

        // Función para crear las intenciones recursivamente
        $createIntents = function ($intents, $parent = null) use (&$createIntents, $chatbot) {
            foreach ($intents as $intentData) {
                // Crear intención
                $intent = Intent::create([
                    'chatbot_id' => $chatbot->id,
                    'id' => $intentData['id'],
                    'name' => $intentData['name'],
                    'is_choice' => $intentData['is_choice'] ?? false,
                    'save_information' => $intentData['save_information'] ?? false,
                    'category' => $intentData['category'] ?? null,
                    'information_required' => $intentData['information_required'] ?? null,
                    'type' => $intentData['type'] ?? null,
                    'position' => json_encode([
                        'x' => $intentData['position']['x'] ?? 0,
                        'y' => $intentData['position']['y'] ?? 0,
                    ]),
                    'data' => json_encode($intentData['data'] ?? []),
                ]);

                // Crear frases de entrenamiento
                if (isset($intentData['phrases'])) {
                    foreach ($intentData['phrases'] as $phrase) {
                        IntentTrainingPhrase::create([
                            'intent_id' => $intent->id,
                            'phrase' => $phrase
                        ]);
                    }
                }

                // Crear respuestas
                if (isset($intentData['responses'])) {
                    foreach ($intentData['responses'] as $response) {
                        IntentResponse::create([
                            'intent_id' => $intent->id,
                            'response' => $response
                        ]);
                    }
                }

                // Crear opciones y transiciones
                if (isset($intentData['options'])) {
                    foreach ($intentData['options'] as $optionText) {
                        $option = IntentOption::create([
                            'id' => Str::uuid(),
                            'intent_id' => $intent->id,
                            'option' => $optionText
                        ]);
                    }
                }

                // Crear nodos hijos recursivamente
                if (isset($intentData['children'])) {
                    $createIntents($intentData['children'], $intent->id);
                }
            }
        };

        // Crear las intenciones recursivamente
        $createIntents($intentsData);
    }
}
