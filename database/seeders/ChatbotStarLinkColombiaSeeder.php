<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Entity\Entity;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Seeder;
use App\Models\Chatbot\Knowledge;
use App\Models\Entity\EntityValue;
use App\Models\Intent\IntentOption;
use App\Models\Intent\IntentResponse;
use App\Enums\TypeInformationRequired;
use App\Models\User\ContactInformation;
use App\Models\Intent\IntentTrainingPhrase;

class ChatbotStarLinkColombiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // hybrid,
        // natural language processing,
        // rule based

        // Datos del chatbot
        $chatbotData = [
            'id' => '4cbda66d-2ba5-470e-956e-037946e96307',
            'user_id' => 1,
            'name' => 'SkynetBot',
            'description' => 'Chatbot para la empresa de telefonía, televisión y internet.',
            'type' => 'Natural language processing'
        ];

        $chatbot = Chatbot::create($chatbotData);

        $intentsData = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Saludo inicial',
                'type' => 'customeNode',
                'category' => 'saludo',
                'position' => [
                    'x' => 0,
                    'y' => 0,
                ],
                'data' => ['label' => 'Saludo inicial'],
                'phrases' => [
                    'Hola',
                    'Buenos días',
                    'Buenas tardes',
                    'Buenas noches',
                    'Hello',
                    'Hi',
                ],
                'responses' => [
                    '¡Hola soy SkynetBot! ¿En qué puedo ayudarte hoy?',
                    '¡Hola soy SkynetBot! ¿cómo puedo asistirte el dia de hoy?',
                ],
            ],

            // Nodo 1: Obtener tipos de planes
            [
                'id' => Str::uuid(),
                'name' => 'Obtener tipos de planes',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 200,
                    'y' => 200,
                ],
                'data' => ['label' => 'Obtener tipos de planes'],
                'phrases' => [
                    'Quiero saber qué tipos de planes tienen',
                    '¿Cuáles son los tipos de planes que manejan?',
                    'Información de planes',
                    'Conocer planes'
                ],
                'responses' => [
                    'Tenemos planes de Internet de $169.000, $249.000 y $349.000 COP al mes. ¿En cuál estás interesado?',
                    'Ofrecemos planes de Internet de 100 Mbps, 200 Mbps y 500 Mbps. ¿Cuál te interesa?'
                ],
                'options' => [
                    'Plan de $169.000 COP al mes',
                    'Plan de $249.000 COP al mes',
                    'Plan de $349.000 COP al mes'
                ]
            ],
            // Preguntas sobre el plan de $169.000 COP al mes
            [
                'id' => Str::uuid(),
                'name' => 'Plan de 50 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 3000,
                    'y' => 200,
                ],
                'data' => ['label' => 'Plan de 50 Mbps'],
                'phrases' => [
                    'Quiero saber sobre el plan de 50 Mbps',
                    '¿Cuál es el plan de internet más económico de Starlink?',
                    'Información sobre el plan de 50 Mbps'
                ],
                'responses' => [
                    'Nuestro plan de 50 Mbps cuesta $154.000 COP al mes. ¿Te interesa?',
                    'Este plan es ideal para una persona o pareja. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre el plan de 50 Mbps',
                    'Preguntar sobre otro plan'
                ]
            ],
            // Nodo 2: Plan de 150 Mbps
            [
                'id' => Str::uuid(),
                'name' => 'Plan de 150 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 3200,
                    'y' => 200,
                ],
                'data' => ['label' => 'Plan de 150 Mbps'],
                'phrases' => [
                    'Quiero saber sobre el plan de 150 Mbps',
                    '¿Cuál es el plan de internet más popular de Starlink?',
                    'Información sobre el plan de 150 Mbps'
                ],
                'responses' => [
                    'Nuestro plan de 150 Mbps cuesta $239.000 COP al mes. ¿Te interesa?',
                    'Este plan es ideal para hogares con varias personas. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre el plan de 150 Mbps',
                    'Preguntar sobre otro plan'
                ]
            ],
            // Nodo 3: Plan de 300 Mbps
            [
                'id' => Str::uuid(),
                'name' => 'Plan de 300 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 3400,
                    'y' => 200,
                ],
                'data' => ['label' => 'Plan de 300 Mbps'],
                'phrases' => [
                    'Quiero saber sobre el plan de 300 Mbps',
                    '¿Cuál es el plan de internet más rápido de Starlink?',
                    'Información sobre el plan de 300 Mbps'
                ],
                'responses' => [
                    'Nuestro plan de 300 Mbps cuesta $329.000 COP al mes. ¿Te interesa?',
                    'Este plan es ideal para hogares con varias personas y uso intensivo. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre el plan de 300 Mbps',
                    'Preguntar sobre otro plan'
                ]
            ],
            // Nodo 4: Plan de 500 Mbps
            [
                'id' => Str::uuid(),
                'name' => 'Plan de 500 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 3600,
                    'y' => 200,
                ],
                'data' => ['label' => 'Plan de 500 Mbps'],
                'phrases' => [
                    'Quiero saber sobre el plan de 500 Mbps',
                    '¿Cuál es el plan de internet más rápido de Starlink?',
                    'Información sobre el plan de 500 Mbps'
                ],
                'responses' => [
                    'Nuestro plan de 500 Mbps cuesta $449.000 COP al mes. ¿Te interesa?',
                    'Este plan es ideal para hogares con varias personas y uso intensivo. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre el plan de 500 Mbps',
                    'Preguntar sobre otro plan'
                ]
            ],
            // Nodo 5: Plan de 1 Gbps
            [
                'id' => Str::uuid(),
                'name' => 'Plan de 1 Gbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 3800,
                    'y' => 200,
                ],
                'data' => ['label' => 'Plan de 1 Gbps'],
                'phrases' => [
                    'Quiero saber sobre el plan de 1 Gbps',
                    '¿Cuál es el plan de internet más rápido de Starlink?',
                    'Información sobre el plan de 1 Gbps'
                ],
                'responses' => [
                    'Nuestro plan de 1 Gbps cuesta $649.000 COP al mes. ¿Te interesa?',
                    'Este plan es ideal para hogares con varias personas y uso intensivo. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre el plan de 1 Gbps',
                    'Preguntar sobre otro plan'
                ]
            ],
            // Preguntas sobre la cobertura
            [
                'id' => Str::uuid(),
                'name' => 'Preguntas sobre la cobertura',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 1000,
                    'y' => 200,
                ],
                'data' => ['label' => 'Preguntas sobre la cobertura'],
                'phrases' => [
                    'Quiero saber si tienen cobertura en mi área',
                    '¿Cuál es la cobertura de Starlink en Colombia?',
                    'Información sobre la cobertura'
                ],
                'responses' => [
                    'Tenemos cobertura en la mayoría de las áreas de Colombia. ¿Quieres verificar la cobertura en tu área?',
                    'Puedes verificar la cobertura en tu área en nuestro sitio web. ¿Quieres hacerlo?'
                ],
                'options' => [
                    'Verificar cobertura',
                    'Preguntar sobre otro tema'
                ]
            ],
            // Conectividad en lugares remotos
            [
                'id' => Str::uuid(),
                'name' => 'Conectividad en lugares remotos',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 2400,
                    'y' => 200,
                ],
                'data' => ['label' => 'Conectividad en lugares remotos'],
                'phrases' => [
                    'Quiero saber si puedo conectarme en lugares remotos',
                    '¿Es posible hacer streaming en lugares remotos con Starlink?',
                    'Información sobre conectividad en lugares remotos'
                ],
                'responses' => [
                    'Sí, puedes conectarte en lugares remotos con Starlink. ¿Te interesa?',
                    'Nuestro sistema de internet es ideal para lugares remotos. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre conectividad en lugares remotos',
                    'Preguntar sobre otro tema'
                ]
                ],

            // Nodo 2: Instalación rápida
            [
                'id' => Str::uuid(),
                'name' => 'Instalación rápida',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 2600,
                    'y' => 200,
                ],
                'data' => ['label' => 'Instalación rápida'],
                'phrases' => [
                    'Quiero saber cómo instalar Starlink',
                    '¿Cuánto tiempo tarda en instalar Starlink?',
                    'Información sobre instalación rápida'
                ],
                'responses' => [
                    'Puedes instalar Starlink en solo dos pasos. ¿Te interesa?',
                    'Nuestra instalación es rápida y fácil. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre instalación rápida',
                    'Preguntar sobre otro tema'
                ]
            ],
            // Nodo 3: Sin contratos
            [
                'id' => Str::uuid(),
                'name' => 'Sin contratos',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 2800,
                    'y' => 200,
                ],
                'data' => ['label' => 'Sin contratos'],
                'phrases' => [
                    'Quiero saber si tengo que firmar un contrato',
                    '¿Hay contratos a largo plazo con Starlink?',
                    'Información sobre sin contratos'
                ],
                'responses' => [
                    'No hay contratos a largo plazo con Starlink. ¿Te interesa?',
                    'Puedes cancelar en cualquier momento sin penalizaciones. ¿Quieres saber más?'
                ],
                'options' => [
                    'Saber más sobre sin contratos',
                    'Preguntar sobre otro tema'
                ]
            ]
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

        // Datos de conocimientos
        $knowledgesData = [
            ['topic' => 'Tipos de planes de internet', 'content' => '
                Plan de $154.000 COP al mes: velocidad de hasta 50 Mbps, ideal para una persona o pareja.
                Plan de $239.000 COP al mes: velocidad de hasta 150 Mbps, ideal para hogares con varias personas.
                Plan de $329.000 COP al mes: velocidad de hasta 300 Mbps, ideal para hogares con varias personas y uso intensivo.
                Plan de $449.000 COP al mes: velocidad de hasta 500 Mbps, ideal para hogares con varias personas y uso intensivo.
                Plan de $649.000 COP al mes: velocidad de hasta 1 Gbps, ideal para hogares con varias personas y uso intensivo.
            '],
            ['topic' => 'Costo mensual', 'content' => '
                El costo mensual incluye el equipo de Starlink (antena y router) y el servicio de internet.
                No hay costos adicionales por instalación o activación.
            '],
            ['topic' => 'Cómo funciona el internet de Starlink', 'content' => '
                Starlink utiliza una constelación de satélites en órbita baja para proporcionar internet de alta velocidad.
                La antena de Starlink se conecta a los satélites y envía la señal a un router, que distribuye la conexión a los dispositivos en el hogar.
                La tecnología de Starlink utiliza la banda de frecuencia Ka para proporcionar velocidades de internet rápidas y confiables.
            '],
            ['topic' => 'Otra información relevante', 'content' => '
                Cobertura: Starlink está disponible en la mayoría de las áreas de Colombia, excepto en algunas zonas muy remotas.
                Latencia: la latencia de Starlink es de alrededor de 20-30 ms, lo que es comparable a los servicios de internet por cable.
                Datos ilimitados: Starlink no tiene límites de datos, por lo que puedes usar el internet sin preocuparte por exceder un límite.
                Portabilidad: la antena de Starlink es portátil, por lo que puedes llevarte el internet contigo a cualquier lugar.
            ']
        ];

        // Crear Conocimientos
        foreach ($knowledgesData as $knowledgeData) {
            Knowledge::create([
                'chatbot_id' => $chatbot->id,
                'content' => $knowledgeData['topic'] . ': ' . $knowledgeData['content']
            ]);
        }

        // Datos de entidades y sus valores
        $entitiesData = [
            [
                'name' => 'Planes',
                'datatype' => 'string',
                'values' => ['Internet', 'Teléfono', 'TV']
            ],
            [
                'name' => 'Plan Internet',
                'datatype' => 'string',
                'values' => ['500 Mbps', '100.000', 'Mes']
            ],
            [
                'name' => 'Plan Telefonía',
                'datatype' => 'string',
                'values' => ['Minutos ilimitados', '40.000', '50 GB', 'Mes']
            ],
            [
                'name' => 'Tipo de servicio',
                'datatype' => 'string',
                'values' => ['Instalación', 'Mantenimiento', 'Actualización']
            ],
            [
                'name' => 'Tipo de problema',
                'datatype' => 'string',
                'values' => ['Conectividad', 'Facturación', 'Soporte técnico']
            ]
        ];

        // Crear Entidades y sus valores
        foreach ($entitiesData as $entityData) {
            $entity = Entity::create([
                'chatbot_id' => $chatbot->id,
                'name' => $entityData['name'],
                'datatype' => $entityData['datatype'],
            ]);

            foreach ($entityData['values'] as $value) {
                EntityValue::create([
                    'entity_id' => $entity->id,
                    'value' => $value
                ]);
            }
        }

        $intentsWithSaveInformation = Intent::where('save_information', true)->get();

        foreach ($intentsWithSaveInformation as $intent) {
            for ($i = 0; $i < 5; $i++) {
                ContactInformation::create([
                    'intent_id' => $intent->id,
                    'value' => "Valor de prueba {$i} para {$intent->name}"
                ]);
            }
        }
    }
}
