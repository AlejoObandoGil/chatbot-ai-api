<?php

namespace Database\Seeders;

use App\Models\User\User;
use App\Models\Entity\Entity;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use Illuminate\Database\Seeder;
use App\Models\Chatbot\Knowledge;
use App\Models\Chatbot\EntityValue;
use App\Models\Intent\IntentResponse;
use App\Models\Intent\IntentTrainingPhrase;

class ChatbotTelecomunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // categoria de intenciones
        // 'greetings',
        // 'only_information',
        // 'save_information',
        // 'goodbyes',
        // 'unanswered',
        // 'errors'

        // $user = User::create([
        //     'name' => 'John',
        //     'email' => 'joanlejo0803@gmail.com',
        //     'password' => bcrypt('Password1234.'),
        // ]);

        // Datos del chatbot
        $chatbotData = [
            'user_id' => 1,
            'name' => 'TelcoBot',
            'description' => 'Chatbot para la empresa de telefonía, televisión y internet.'
        ];

        $chatbot = Chatbot::create($chatbotData);

         // Datos de las intenciones
        $intentsData = [
            [
                'name' => 'Saludo inicial',
                'level' => 1,
                'intent_category_id' => 1,
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
                ]
            ],
            [
                'name' => 'Obtener información',
                'level' => 1,
                'intent_category_id' => 2,
                'children' => [
                    [
                        'name' => 'Obtener tipos de planes',
                        'level' => 2,
                        'intent_category_id' => 2,
                        'phrases' => [
                            'Quiero saber sobre el plan de internet',
                            '¿Qué tipo de planes tienen?'
                        ],
                        'responses' => [
                            'Nuestro plan de Internet ofrece alta velocidad y precios competitivos.',
                            'Ofrecemos planes de Internet, Teléfono y TV. ¿Cuál te interesa?'
                        ],
                        'children' => [
                            [
                                'name' => 'Comprar un plan de Internet',
                                'level' => 3,
                                'intent_category_id' => 3,
                                'phrases' => [
                                    'Me gustaría comprar un plan de internet',
                                    'Quiero saber el precio del plan de internet'
                                ],
                                'responses' => [
                                    'Por favor escriba su numero de documento, y lo contactaremos a la brevedad.',
                                    'El plan de internet tiene un precio de $100.000 por mes.'
                                ]
                            ],
                            [
                                'name' => 'Comprar un plan de Telefonía',
                                'level' => 3,
                                'intent_category_id' => 3,
                                'phrases' => [
                                    'Me gustaría comprar un plan de telefonía',
                                    'Quiero saber el precio del plan de telefonía'
                                ],
                                'responses' => [
                                    'Por favor escriba su numero de documento, lo contactaremos a la brevedad.',
                                    'El plan de telefónía tiene un precio de $40.000 por mes.'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Reportar un Problema',
                'level' => 1,
                'intent_category_id' => 2,
                'children' => [
                    [
                        'name' => 'Reportar un Problema de Conectividad',
                        'level' => 2,
                        'intent_category_id' => 2,
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
                        'name' => 'Reportar un Problema de Facturación',
                        'level' => 2,
                        'intent_category_id' => 2,
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
                'name' => 'Solicitar un Servicio',
                'level' => 1,
                'intent_category_id' => 2,
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
                'name' => 'Despedida',
                'level' => 0,
                'intent_category_id' => 4,
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
            ],
            // [
            //     'name' => 'No Coincidencia',
            //     'level' => 1,
            //     'intent_category_id' => 5,
            //     'responses' => [
            //         'Lo siento, no tengo una respuesta para eso. ¿Podrías intentar preguntar de otra manera?',
            //         'No estoy seguro de cómo responder eso. Por favor, intenta con una pregunta diferente.'
            //     ]
            // ]
            // [
            //     'name' => 'Error',
            //     'level' => 1,
            //     'intent_category_id' => 6,
            //     'phrases' => [
            //         'Hubo un error',
            //         'No entendí tu mensaje'
            //     ],
            //     'responses' => [
            //         'Lo siento, ha ocurrido un error. Por favor, intenta nuevamente.',
            //         'Parece que hubo un problema. ¿Podrías intentar decirlo de otra manera?'
            //     ]
            // ],
        ];

        // Función para crear las intenciones recursivamente
        $createIntents = function ($intents, $parent = null) use (&$createIntents, $chatbot) {
            foreach ($intents as $intentData) {
                $intent = Intent::create([
                    'chatbot_id' => $chatbot->id,
                    'name' => $intentData['name'],
                    'parent_id' => $parent ? $parent->id : null,
                ]);

                if (isset($intentData['phrases'])) {
                    foreach ($intentData['phrases'] as $phrase) {
                        IntentTrainingPhrase::create([
                            'intent_id' => $intent->id,
                            'phrase' => $phrase
                        ]);
                    }
                }

                if (isset($intentData['responses'])) {
                    foreach ($intentData['responses'] as $response) {
                        IntentResponse::create([
                            'intent_id' => $intent->id,
                            'response' => $response
                        ]);
                    }
                }

                if (isset($intentData['children'])) {
                    $createIntents($intentData['children'], $intent);
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
                'topic' => $knowledgeData['topic'],
                'content' => $knowledgeData['content']
            ]);
        }

        // Datos de entidades y sus valores
        $entitiesData = [
            [
                'name' => 'Tipo de plan',
                'type' => 'string',
                'save_information' => true,
                'values' => ['Internet', 'Teléfono', 'TV']
            ],
            [
                'name' => 'Saldo de cliente',
                'type' => 'string',
                'save_information' => true,
                'values' => ['100.000']
            ],
            [
                'name' => 'Tipo de servicio',
                'type' => 'string',
                'save_information' => true,
                'values' => ['Instalación', 'Mantenimiento', 'Actualización']
            ],
            [
                'name' => 'Tipo de problema',
                'type' => 'string',
                'save_information' => true,
                'values' => ['Conectividad', 'Facturación', 'Soporte técnico']
            ]
        ];

        // Crear Entidades y sus valores
        foreach ($entitiesData as $entityData) {
            $entity = Entity::create([
                'chatbot_id' => $chatbot->id,
                'name' => $entityData['name'],
                'type' => $entityData['type'],
                'save_information' => $entityData['save_information']
            ]);

            foreach ($entityData['values'] as $value) {
                EntityValue::create([
                    'entity_id' => $entity->id,
                    'value' => $value
                ]);
            }
        }
    }
}
