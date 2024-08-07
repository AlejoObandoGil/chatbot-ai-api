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
        // Híbrido,
        // PLN,
        // Basado en reglas

        // Datos del chatbot
        $chatbotData = [
            'id' => '06e4e314-f510-44df-a849-71d2d85dd568',
            'user_id' => 1,
            'name' => 'SkynetBot',
            'description' => 'Chatbot para la empresa de elon mocs.',
            'type' => 'Basado en reglas'
        ];

        $chatbot = Chatbot::create($chatbotData);

        $intentsData = [
            [
                'id' => 'ffeb4039-581f-4baa-82a9-92184733a127',
                'name' => 'Saludo inicial',
                'is_choice' => true,
                'type' => 'customNode',
                'category' => 'saludo',
                'position' => [
                    'x' => -100,
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
                    'Otro tema de interés'
                ],
                'responses' => [
                    '¡Hola soy SkynetBot! ¿En qué puedo ayudarte hoy? Selecciona una opción:',
                    '¡Hola soy SkynetBot! Tenemos varios temas que podrían interesarte. ¿Cuál te gustaría explorar?'
                ],
                'options' => [
                    'Tipos de planes de internet',
                    'Cómo funciona el internet de Starlink',
                    'Tipo de contrato',
                    'Información sobre la cobertura',
                    'Otra información relevante',
                ]
            ],
            // Nodo 1: Obtener tipos de planes
            [
                'id' => '4b80ee8b-0f64-490c-9bf0-67e1ae8731b2',
                'name' => 'Obtener tipos de planes',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -500,
                    'y' => 300,
                ],
                'data' => ['label' => 'Obtener tipos de planes'],
                'phrases' => [
                    'Quiero saber qué tipos de planes de internet',
                    '¿Cuáles son los tipos de planes que manejan?',
                    'Información de planes',
                    'Tipos de planes de internet',
                    'Planes de internet para hogares',
                    'Preguntar sobre otro plan',
                ],
                'responses' => [
                    'Tenemos planes de Internet de $154.000, $239.000, $329.000, $449.000 y 649.000 COP al mes. ¿En cuál estás interesado?',
                    'Ofrecemos planes de Internet de 50 Mbps, 150 Mbps, 300 Mbps, 500 Mbps y 1 Gbps. ¿Cuál te interesa?'
                ],
                'options' => [
                    'Plan de $154.000 COP al mes',
                    'Plan de $239.000 COP al mes',
                    'Plan de $329.000 COP al mes',
                    'Plan de $449.000 COP al mes',
                    'Plan de $649.000 COP al mes',
                ]
            ],
            // Preguntas sobre el plan de $169.000 COP al mes
            [
                'id' => '556bd4d9-6579-4923-9494-2e2de059a3c7',
                'name' => 'Plan de 50 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -2000,
                    'y' => 600,
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
                    'Comprar',
                    'Preguntar sobre otro plan'
                ],
                'children' => [
                    [
                        'id' => '010d935d-f891-43d6-8b18-ae58fcf699e2',
                        'name' => 'Comprar Plan de 50 Mbps',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::TELEFONO,
                        'position' => [
                            'x' => -2000,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Comprar Plan de 50 Mbps'],
                        'phrases' => [
                            'Comprar',
                            'Comprar Plan de 50 Mbps',
                            'Comprar plan de 154.000',
                            'Comprar plan de 50 Mbps de $154.000 COP'
                        ],
                        'responses' => [
                            'Por favor escriba su número de teléfono, lo contactaremos a la brevedad.'
                        ],
                    ],
                ]
            ],
            // Nodo 2: Plan de 150 Mbps
            [
                'id' => '9977b1c6-d663-4b95-8f52-498e530eef4b',
                'name' => 'Plan de 150 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -1700,
                    'y' => 600,
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
                    'Comprar',
                    'Preguntar sobre otro plan'
                ],
                'children' => [
                    [
                        'id' => Str::uuid(),
                        'name' => 'Comprar Plan de 150 Mbps guardar teléfono',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::TELEFONO,
                        'position' => [
                            'x' => -1700,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Comprar Plan de 150 Mbps guardar teléfono'],
                        'phrases' => [
                            'Comprar',
                            'Comprar Plan de 150 Mbps',
                            'Comprar plan de 239.000',
                            'Comprar plan de 150 Mbps de $239.000 COP'
                        ],
                        'responses' => [
                            'Por favor escriba los siguientes datos: Escriba su número de teléfono.'
                        ],
                    ],
                    [
                        'id' => Str::uuid(),
                        'name' => 'Comprar Plan de 150 Mbps guardar documento',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::NUMERO_DE_DOCUMENTO,
                        'position' => [
                            'x' => -1700,
                            'y' => 1300,
                        ],
                        'data' => ['label' => 'Comprar Plan de 150 Mbps guardar documento'],
                        'phrases' => [],
                        'responses' => [
                            'Escriba su número de documento.'
                        ],
                    ],
                    [
                        'id' => 'f036fb9c-a088-4554-819b-b142804365fe',
                        'name' => 'Comprar Plan de 150 Mbps guardar nombre completo',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::NOMBRE_COMPLETO,
                        'position' => [
                            'x' => -1700,
                            'y' => 1800,
                        ],
                        'data' => ['label' => 'Comprar Plan de 150 Mbps guardar nombre completo'],
                        'phrases' => [],
                        'responses' => [
                            'Por último escriba su nombre completo, y lo contactaremos a la brevedad.'
                        ],
                    ],
                    [
                        'id' => Str::uuid(),
                        'name' => 'Comprar Plan de 150 Mbps respuesta',
                        'type' => 'customeNode',
                        'position' => [
                            'x' => -1700,
                            'y' => 2100,
                        ],
                        'data' => ['label' => 'Comprar Plan de 150 Mbps respuesta'],
                        'phrases' => [],
                        'responses' => [
                            'Hemos guardado tu información, un asesor se pondará en contacto, garcias por preferir Starlink, ¿Tienes alguna otra pregunta?.',
                        ],
                    ],
                ]
            ],
            // Nodo 3: Plan de 300 Mbps
            [
                'id' => 'b9d965d0-a98a-4539-ac89-4b01d0a52552',
                'name' => 'Plan de 300 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -1400,
                    'y' => 600,
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
                    'Comprar',
                    'Preguntar sobre otro plan'
                ],
                'children' => [
                    [
                        'id' => '137fa06d-b7dc-4be2-b09d-4ccbe8c8584a',
                        'name' => 'Comprar Plan de 300 Mbps',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::TELEFONO,
                        'position' => [
                            'x' => -1400,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Comprar Plan de 300 Mbps'],
                        'phrases' => [
                            'Comprar',
                            'Comprar Plan de 300 Mbps',
                            'Comprar plan de 329.000',
                            'Comprar plan de 300 Mbps de $329.000 COP'
                        ],
                        'responses' => [
                            'Por favor escriba su número de teléfono, lo contactaremos a la brevedad.'
                        ],
                    ],
                ]
            ],
            // Nodo 4: Plan de 500 Mbps
            [
                'id' => '3c2949e3-eb69-498a-9f15-1b74c75b1677',
                'name' => 'Plan de 500 Mbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -1100,
                    'y' => 600,
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
                    'Comprar',
                    'Preguntar sobre otro plan'
                ],
                'children' => [
                    [
                        'id' => '5ec2b4c5-a690-48d3-a1a9-3fe6944c2f55',
                        'name' => 'Comprar Plan de 500 Mbps',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::TELEFONO,
                        'position' => [
                            'x' => -1100,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Comprar Plan de 500 Mbps'],
                        'phrases' => [
                            'Comprar',
                            'Comprar Plan de 500 Mbps',
                            'Comprar plan de 449.000',
                            'Comprar plan de 500 Mbps de $449.000 COP'
                        ],
                        'responses' => [
                            'Por favor escriba su número de teléfono, lo contactaremos a la brevedad.'
                        ],
                    ],
                ]
            ],
            // Nodo 5: Plan de 1 Gbps
            [
                'id' => 'be112d5b-a84e-455b-a3ff-1b7cee708256',
                'name' => 'Plan de 1 Gbps',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -800,
                    'y' => 600,
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
                    'Comprar',
                    'Preguntar sobre otro plan'
                ],
                'children' => [
                    [
                        'id' => 'ebe91c60-f981-4a83-b6a3-99a1bec0466b',
                        'name' => 'Comprar Plan de 1 Gbps',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::TELEFONO,
                        'position' => [
                            'x' => -800,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Comprar Plan de 1 Gbps'],
                        'phrases' => [
                            'Comprar',
                            'Comprar Plan de 1 Gbps',
                            'Comprar plan de 649.000',
                            'Comprar plan de 1 Gbps de $649.000 COP'
                        ],
                        'responses' => [
                            'Por favor escriba su número de teléfono, lo contactaremos a la brevedad.'
                        ],
                    ],
                    [
                        'id' => Str::uuid(),
                        'name' => 'Comprar Plan de 1 Gbps respuesta',
                        'type' => 'customeNode',
                        'position' => [
                            'x' => -800,
                            'y' => 1300,
                        ],
                        'data' => ['label' => 'Comprar Plan de 1 Gbps respuesta'],
                        'phrases' => [],
                        'responses' => [
                            'Hemos guardado su información, garcias por usar Starlink, ¿Tienes alguna otra pregunta?.',
                        ],
                    ],
                ]
            ],
            // Preguntas sobre la cobertura
            [
                'id' => '8c66c072-c2c6-4976-bfce-10e6fb2b7a36',
                'name' => 'Preguntas sobre la cobertura',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -500,
                    'y' => 600,
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
                ],
                'children' => [
                    [
                        'id' => Str::uuid(),
                        'name' => 'Verificar cobertura y guardar ciudad',
                        'type' => 'customeNode',
                        'save_information' => true,
                        'information_required' => TypeInformationRequired::CIUDAD,
                        'position' => [
                            'x' => -500,
                            'y' => 900,
                        ],
                        'data' => ['label' => 'Verificar cobertura y guardar ciudad'],
                        'phrases' => [
                            'Verificar cobertura',
                            'Verificar cobertura en mi ciudad',
                        ],
                        'responses' => [
                            'Por favor escriba su ciudad, para verificar la cobertura.'
                        ],
                    ],
                    [
                        'id' => Str::uuid(),
                        'name' => 'Verificar cobertura respuesta',
                        'type' => 'customeNode',
                        'position' => [
                            'x' => -500,
                            'y' => 1300,
                        ],
                        'data' => ['label' => 'Verificar cobertura respuesta'],
                        'phrases' => [],
                        'responses' => [
                            'Su ciudad cuenta con cobertura de Starlink.',
                            'Su ciudad no cuenta con cobertura de Starlink.'
                        ],
                    ],
                ]
            ],
            // Conectividad en lugares remotos
            [
                'id' => 'b7911db4-7305-48f1-b2c6-e2118daabb79',
                'name' => 'Conectividad en lugares remotos',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -200,
                    'y' => 1200,
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
            ],
            // Nodo 2: Instalación rápida
            [
                'id' => '23fcc54f-4c04-41d6-b43f-3986fe189eab',
                'name' => 'Instalación rápida',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => -200,
                    'y' => 900,
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
            ],
            // Nodo 3: Sin contratos
            [
                'id' => '94219252-f4b7-4943-971a-5b31b7d7b77e',
                'name' => 'Sin contratos',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 100,
                    'y' => 600,
                ],
                'data' => ['label' => 'Sin contratos'],
                'phrases' => [
                    'Quiero saber si tengo que firmar un contrato',
                    '¿Hay contratos a largo plazo con Starlink?',
                    'Información sobre sin contratos',
                    'Tipo de contrato'
                ],
                'responses' => [
                    'No hay contratos a largo plazo con Starlink. ¿Te interesa?',
                    'Puedes cancelar en cualquier momento sin penalizaciones. ¿Quieres saber más?'
                ],
            ],
            [
                'id' => 'b1f8f67a-6d3f-471f-85de-aa5236efd978',
                'name' => 'Cómo funciona el internet de Starlink',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 400,
                    'y' => 600,
                ],
                'data' => ['label' => 'Cómo funciona el internet de Starlink'],
                'phrases' => [
                    'Cómo funciona el internet de Starlink',
                    'Quiero saber cómo funciona el internet de Starlink',
                    '¿Cómo se conecta el internet de Starlink?',
                    'Información sobre la tecnología de Starlink'
                ],
                'responses' => [
                    'Starlink utiliza una constelación de satélites en órbita baja para proporcionar internet de alta velocidad. La antena de Starlink se conecta a los satélites y envía la señal a un router, que distribuye la conexión a los dispositivos en el hogar. La tecnología de Starlink utiliza la banda de frecuencia Ka para proporcionar velocidades de internet rápidas y confiables.',
                    'Nuestra tecnología es innovadora y permite una conexión rápida y segura. ¿Quieres saber más sobre nuestros planes?'
                ],
            ],
                // Nodo 2: Otra información relevante
            [
                'id' => '2789f73b-d11d-4399-af79-5c00810a6ebb',
                'name' => 'Otra información relevante',
                'is_choice' => true,
                'type' => 'customNode',
                'position' => [
                    'x' => 700,
                    'y' => 600,
                ],
                'data' => ['label' => 'Otra información relevante'],
                'phrases' => [
                    'Información',
                    'Información sobre Starlink',
                    'Información sobre internet de Starlink',
                    'Información sobre cobertura de Starlink',
                    'Quiero saber más sobre la cobertura de Starlink',
                    '¿Cuál es la latencia de Starlink?',
                    'Información sobre los datos ilimitados de Starlink',
                    'Saber más sobre la tecnología de Starlink'
                ],
                'responses' => [
                    'Starlink está disponible en la mayoría de las áreas de Colombia, excepto en algunas zonas muy remotas. La latencia de Starlink es de alrededor de 20-30 ms, lo que es comparable a los servicios de internet por cable. Además, Starlink no tiene límites de datos, por lo que puedes usar el internet sin preocuparte por exceder un límite. La antena de Starlink es portátil, por lo que puedes llevarte el internet contigo a cualquier lugar.',
                    'Nuestros servicios son ideales para aquellos que buscan una conexión rápida y segura.'
                ],
            ],
            [
                'id' => 'f7e4a474-9ab7-4019-8f2a-3340efbb08f4', // Despedida
                'name' => 'Despedida',
                'category' => 'despedida',
                'is_choice' => false,
                'type' => 'customNode',
                'position' => [
                    'x' => 1000,
                    'y' => 600,
                ],
                'data' => ['label' => 'Despedida'],
                'phrases' => [
                    'Gracias',
                    'Eso es todo por ahora',
                    'Adiós',
                    'Hasta luego',
                ],
                'responses' => [
                    '¡Gracias por utilizar SkynetBot! Estamos aquí para ayudarte en cualquier momento.',
                    '¡Que tengas un excelente día! No dudes en volver si necesitas más información.',
                ],
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
