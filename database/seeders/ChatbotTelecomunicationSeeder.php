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
use App\Models\User\ContactInformation;
use App\Models\Intent\IntentTrainingPhrase;

class ChatbotTelecomunicationSeeder extends Seeder
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
            'name' => 'TelcoBot',
            'description' => 'Chatbot para la empresa de telefonía, televisión y internet.',
            'type' => 'Natural language processing'
        ];

        $chatbot = Chatbot::create($chatbotData);

        $intentsData = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Saludo inicial',
                'type' => 'customeNode',
                'position' => [
                    'x' => 0,
                    'y' => 0,
                ],
                'data' => ['label' => 'Saludo inicial'],
                'phrases' => [
                    'Hola',
                    'Buenos días',
                    'Buenas tardes',
                    'Buenas noches'
                ],
                'responses' => [
                    '¡Hola! ¿En qué puedo ayudarte hoy?',
                    'Buenos días, ¿cómo puedo asistirte?',
                    'Buenas tardes, ¿en qué puedo ayudarte?',
                    'Buenas noches, ¿cómo puedo asistirte?'
                ],
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Obtener información',
                'type' => 'customeNode',
                'position' => [
                    'x' => 100,
                    'y' => 100,
                ],
                'data' => ['label' => 'Obtener información'],
                'children' => [
                    [
                        'id' => (string) Str::uuid(),
                        'name' => 'Obtener tipos de planes',
                        'is_choice' => true,
                        'type' => 'customeNode',
                        'position' => [
                            'x' => 200,
                            'y' => 200,
                        ],
                        'data' => ['label' => 'Obtener tipos de planes'],
                        'phrases' => [
                            'Quiero saber sobre el plan de internet',
                            '¿Qué tipo de planes tienen?'
                        ],
                        'responses' => [
                            'Nuestro plan de Internet ofrece alta velocidad y precios competitivos.',
                            'Ofrecemos planes de Internet, Teléfono y TV. ¿Cuál te interesa?'
                        ],
                        'options' => [
                            'Internet',
                            'Telefónía',
                            'TV'
                        ],
                        'children' => [
                            [
                                'id' => (string) Str::uuid(),
                                'name' => 'Plan de Internet',
                                'is_choice' => true,
                                'type' => 'customeNode',
                                'position' => [
                                    'x' => 500,
                                    'y' => 300,
                                ],
                                'data' => ['label' => 'Plan de Internet'],
                                'phrases' => [
                                    'Internet',
                                    'Quiero saber el precio del plan de internet'
                                ],
                                'responses' => [
                                    'El plan de internet tiene un precio de $100.000 por mes, 500 Mbps de velocidad.'
                                ],
                                'options' => [
                                    'Comprar',
                                    'Regresar a todos los planes'
                                ],
                                'children' => [
                                    [
                                        'id' => (string) Str::uuid(),
                                        'name' => 'Comprar plan de Internet',
                                        'save_information' => true,
                                        'type' => 'customeNode',
                                        'position' => [
                                            'x' => 600,
                                            'y' => 400,
                                        ],
                                        'data' => ['label' => 'Comprar plan de Internet'],
                                        'phrases' => [
                                            'Comprar',
                                            'Comprar plan de internet de 100.000 por mes, 500 Mbps de velocidad'
                                        ],
                                        'responses' => [
                                            'Por favor escriba su Nombre completo y número de teléfono separado por comas, lo contactaremos a la brevedad.'
                                        ],
                                        'children' => [
                                            [
                                                'id' => (string) Str::uuid(),
                                                'name' => 'Comprar plan de Internet respuesta final',
                                                'type' => 'customeNode',
                                                'position' => [
                                                    'x' => -100,
                                                    'y' => 400,
                                                ],
                                                'data' => ['label' => 'Comprar plan de Internet respuesta final'],
                                                'phrases' => [
                                                    'Nombre',
                                                    'Teléfono'
                                                ],
                                                'responses' => [
                                                    'Hemos guardado su información, un asesor se contactará con usted.'
                                                ],
                                            ]
                                        ]
                                    ]
                                ],
                            ],
                            [
                                'id' => (string) Str::uuid(),
                                'name' => 'Plan de Telefonía',
                                'type' => 'customeNode',
                                'is_choice' => true,
                                'position' => [
                                    'x' => 100,
                                    'y' => 300,
                                ],
                                'data' => ['label' => 'Plan de Telefonía'],
                                'phrases' => [
                                    'Telefonía',
                                    'Quiero saber el precio del plan de telefonía'
                                ],
                                'responses' => [
                                    'El plan de telefonía tiene un precio de $40.000 por mes, con minutos ilimitados y 50 GB de datos.'
                                ],
                                'options' => [
                                    'Comprar',
                                    'Regresar a Todos los planes'
                                ],
                                'children' => [
                                    [
                                        'id' => (string) Str::uuid(),
                                        'name' => 'Comprar plan de telefonía',
                                        'save_information' => true,
                                        'type' => 'customeNode',
                                        'position' => [
                                            'x' => -100,
                                            'y' => 400,
                                        ],
                                        'data' => ['label' => 'Comprar plan de telefonía'],
                                        'phrases' => [
                                            'Comprar',
                                            'Comprar plan de telefónía de $40.000 por mes'
                                        ],
                                        'responses' => [
                                            'Por favor escriba su Nombre completo y número de teléfono separado por comas, lo contactaremos a la brevedad.'
                                        ],
                                        'children' => [
                                            [
                                                'id' => (string) Str::uuid(),
                                                'name' => 'Comprar plan de telefonía respuesta final',
                                                'type' => 'customeNode',
                                                'position' => [
                                                    'x' => -100,
                                                    'y' => 400,
                                                ],
                                                'data' => ['label' => 'Comprar plan de telefonía respuesta final'],
                                                'phrases' => [
                                                    'Nombre',
                                                    'Teléfono'
                                                ],
                                                'responses' => [
                                                    'Hemos guardado su información, un asesor se contactará con usted.'
                                                ],
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Reportar un Problema',
                'type' => 'customeNode',
                'position' => [
                    'x' => 100,
                    'y' => 500,
                ],
                'data' => ['label' => 'Reportar un Problema'],
                'children' => [
                    [
                        'id' => (string) Str::uuid(),
                        'name' => 'Reportar un Problema de Conectividad',
                        'type' => 'customeNode',
                        'position' => [
                            'x' => 400,
                            'y' => 600,
                        ],
                        'data' => ['label' => 'Reportar un Problema de Conectividad'],
                        'phrases' => [
                            'Tengo problemas con la conexión a internet',
                            'Mi internet no funciona correctamente'
                        ],
                        'responses' => [
                            'Lamentamos escuchar eso. ¿Podrías proporcionar más detalles sobre el problema de conectividad?',
                            '¿Estás experimentando problemas de conectividad en un dispositivo específico?'
                        ]
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'name' => 'Reportar un Problema de Facturación',
                        'type' => 'customeNode',
                        'position' => [
                            'x' => 0,
                            'y' => 600,
                        ],
                        'data' => ['label' => 'Reportar un Problema de Facturación'],
                        'phrases' => [
                            'Tengo un problema con mi factura',
                            'Mi factura no es correcta'
                        ],
                        'responses' => [
                            'Entendido. Vamos a revisar tu factura. ¿Podrías proporcionar más detalles?',
                            '¿El problema con tu factura está relacionado con algún cargo específico?'
                        ]
                    ]
                ]
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Solicitar un Servicio',
                'type' => 'customeNode',
                'position' => [
                    'x' => 100,
                    'y' => 700,
                ],
                'data' => ['label' => 'Solicitar un Servicio'],
                'phrases' => [
                    'Quiero solicitar un servicio',
                    'Necesito mantenimiento para mi internet'
                ],
                'responses' => [
                    '¿Qué tipo de servicio necesitas?',
                    'Entendido. Vamos a proceder con tu solicitud.'
                ]
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Despedida',
                'type' => 'customeNode',
                'position' => [
                    'x' => 0,
                    'y' => 800,
                ],
                'data' => ['label' => 'Despedida'],
                'phrases' => [
                    'Adiós',
                    'Hasta luego',
                    'Nos vemos',
                    'Hasta pronto',
                    'Que tengas un buen día'
                ],
                'responses' => [
                    '¡Adiós! Que tengas un buen día.',
                    'Hasta luego, que estés bien.',
                    'Nos vemos, que todo vaya bien.',
                    'Hasta pronto, cuídate.',
                    'Que tengas un buen día, ¡nos vemos pronto!'
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
            ['topic' => 'Planes de Internet', 'content' => 'Ofrecemos planes de internet de alta velocidad para hogar y oficina.'],
            ['topic' => 'Planes de Telefonía', 'content' => 'Nuestros planes de telefonía incluyen llamadas ilimitadas y roaming internacional.'],
            ['topic' => 'Planes de TV', 'content' => 'Ofrecemos una variedad de canales de entretenimiento, deportes y noticias.'],
            ['topic' => 'Soporte Técnico', 'content' => 'Nuestro equipo de soporte técnico está disponible 24/7 para resolver cualquier problema.']
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
