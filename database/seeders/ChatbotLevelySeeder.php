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

class ChatbotLevelySeeder extends Seeder
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
            'id' => 'eed9f0aa-add0-433f-a497-927a792a0c0a',
            'user_id' => 1,
            'name' => 'LevelBot',
            'description' => 'Eres un chatbot encargado de brindar asistencia a los usuarios sobre la plataforma de levely.',
            'type' => 'Híbrido',
            'assistant_openai_id' => 'asst_96RBTTingTHmqV2VBhvXaM5z',
            'temperature' => 0.5,
            'max_tokens' => 50
        ];

        $chatbot = Chatbot::create($chatbotData);

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
                    'Otro tema de interés'
                ],
                'responses' => [
                    '¡Hola soy levelBot! ¿En qué puedo asesorarte el dia de hoy? Selecciona una opción o escribe tu pregunta',
                    '¡Hola soy levelBot! Tenemos varios temas que podrían interesarte. ¿Cuál te gustaría explorar? Selecciona una opción o escribe tu pregunta'
                ],
                'options' => [
                    'Comprar una subscripción',
                    'Soporte técnico',
                    'Agendar una demo',
                    'Información sobre Levely',
                    'Aprender a usar Levely',
                ]
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Comprar una subscripción guardar correo',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::CORREO,
                'position' => [
                    'x' => -400,
                    'y' => 300,
                ],
                'data' => ['label' => 'Comprar una subscripción guardar correo'],
                'phrases' => [
                    'Comprar',
                    'Comprar una subscripción',
                    'Comprar licencias',
                    'Comprar levely'
                ],
                'responses' => [
                    'Por favor escriba su correo electrónico para brindarle una subscripción.'
                ]
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Comprar una subscripción guardar nombre',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::NOMBRE_COMPLETO,
                'position' => [
                    'x' => -400,
                    'y' => 600,
                ],
                'data' => ['label' => 'Comprar una subscripción guardar nombre'],
                'phrases' => [],
                'responses' => [
                    'Por favor escriba su nombre completo.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Comprar una subscripción guardar nombre empresa',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::EMPRESA,
                'position' => [
                    'x' => -400,
                    'y' => 900,
                ],
                'data' => ['label' => 'Comprar una subscripción guardar nombre empresa'],
                'phrases' => [],
                'responses' => [
                    'Por último por favor escriba el nombre de su institución y lo contactaremos enseguida.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Comprar una subscripción respuesta final',
                'type' => 'customeNode',
                'position' => [
                    'x' => -400,
                    'y' => 1100,
                ],
                'data' => ['label' => 'Comprar una subscripción respuesta final'],
                'phrases' => [],
                'responses' => [
                    'Hemos guardado tu información, un asesor se pondrá en contacto, ¿Tienes alguna otra pregunta o necesitas alguna otra ayuda?.',
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Soporte técnico guardar teléfono',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::TELEFONO,
                'position' => [
                    'x' => 0,
                    'y' => 300,
                ],
                'data' => ['label' => 'Soporte técnico guardar teléfono'],
                'phrases' => [
                    'Soporte técnico',
                    'Necesito ayuda',
                    'Necesito soporte técnico',
                    'Necesito soporte'
                ],
                'responses' => [
                    'Por favor escriba su número de teléfono para brindarle soporte técnico.'
                ]
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Soporte técnico guardar empresa',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::EMPRESA,
                'position' => [
                    'x' => 0,
                    'y' => 600,
                ],
                'data' => ['label' => 'Soporte técnico guardar empresa'],
                'phrases' => [],
                'responses' => [
                    'Por favor escriba su número de nombre completo y lo contactaremos enseguida.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Soporte técnico respuesta final',
                'type' => 'customeNode',
                'position' => [
                    'x' => 0,
                    'y' => 800,
                ],
                'data' => ['label' => 'Soporte técnico respuesta final'],
                'phrases' => [],
                'responses' => [
                    'Hemos guardado tu información, un asesor se pondrá en contacto, ¿Tienes alguna otra pregunta o necesitas alguna otra ayuda?.',
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo guardar nombre completo',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::NOMBRE_COMPLETO,
                'position' => [
                    'x' => 400,
                    'y' => 300,
                ],
                'data' => ['label' => 'Agendar una demo guardar nombre completo'],
                'phrases' => [
                    'Agendar una demo',
                    'Quiero una demo',
                    'Quiero agendar una demo',
                    'Quiero una prueba',
                    'Agendar prueba'
                ],
                'responses' => [
                    'Le pediremos algunos datos para agendar su demo, Por favor escriba su nombre completo'
                ]
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo guardar teléfono',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::TELEFONO,
                'position' => [
                    'x' => 400,
                    'y' => 600,
                ],
                'data' => ['label' => 'Agendar una demo guardar teléfono'],
                'phrases' => [],
                'responses' => [
                    'Por favor escriba su número de teléfono.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo guardar correo',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::CORREO,
                'position' => [
                    'x' => 400,
                    'y' => 900,
                ],
                'data' => ['label' => 'Agendar una demo guardar correo'],
                'phrases' => [],
                'responses' => [
                    'Por último favor escriba su correo.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo guardar país',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::PAIS,
                'position' => [
                    'x' => 400,
                    'y' => 1100,
                ],
                'data' => ['label' => 'Agendar una demo guardar país'],
                'phrases' => [],
                'responses' => [
                    'Por último favor escriba el nombre de su institución y lo contactaremos enseguida.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo guardar posición',
                'type' => 'customeNode',
                'save_information' => true,
                'information_required' => TypeInformationRequired::PROFESION,
                'position' => [
                    'x' => 400,
                    'y' => 1300,
                ],
                'data' => ['label' => 'Agendar una demo guardar posición'],
                'phrases' => [],
                'responses' => [
                    'Por último favor escriba su posición y lo contactaremos enseguida.'
                ],
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Agendar una demo respuesta final',
                'type' => 'customeNode',
                'position' => [
                    'x' => 400,
                    'y' => 1500,
                ],
                'data' => ['label' => 'Agendar una demo respuesta final'],
                'phrases' => [],
                'responses' => [
                    'Hemos guardado tu información, un asesor se pondrá en contacto, ¿Tienes alguna otra pregunta o necesitas alguna otra información?.',
                ],
            ],
            // Despedida
            [
                'id' => Str::uuid(),
                'name' => 'Despedida',
                'category' => 'despedida',
                'is_choice' => false,
                'type' => 'customNode',
                'position' => [
                    'x' => 600,
                    'y' => 300,
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
        $knowledgesData =
            '¿Cómo crear un curso en Levely?
                1. Dirígete a crear curso.
                2. Agrega un título a tu curso.
                3. Agrega una descripción.
                4. Agrega un video o imagen de fondo para tu curso.
                5. Agrega tus tutores o profesores.
                6. Selecciona la categoría de tu curso.
                7. Selecciona criterios y objetivos.
                8. Selecciona tus etiquetas del curso.
                9. Selecciona el nivel del curso: básico, intermedio, avanzado.
                10. Selecciona el tipo de curso: abierto o privado.
                11. Selecciona si quieres entregar una certificación para los usuarios que aprueben tu curso.
                12. Agrega un formato o plantilla.
                13. Crea tus módulos.
                14. Crea lecciones dentro de tus módulos.
                15. En cada lección tienes múltiples opciones de recursos y contenido para tu curso, algunos de ellos son:
                    - Imagen
                    - Video
                    - Documentos
                    - Links embebidos
                    - Texto
                    - Tareas
                    - Quizzes
                    - Reuniones
                    - Foros
                16. Como último paso, publica tu curso.
                Funciones adicionales de la plataforma
                1. Agregar recursos a favoritos.
                2. Calificar el desempeño de tus estudiantes.
                3. Crear roles y permisos para tus usuarios.
                4. Agregar usuarios por API REST o a través de un documento Excel.
                5. Crear grupos.
                6. Personalizar tu rango de notas.
                7. Chatear con tus profesores o alumnos a través del inbox de Levely.
                8. Estar al tanto de tus exámenes, tareas y reuniones a través del calendario de Levely.
                9. Ver el historial de tus licencias y suscripciones, y mucho más';

        // Crear Conocimientos
        Knowledge::create([
            'chatbot_id' => $chatbot->id,
            'content' => $knowledgesData,
            'document' => 'documents/RH4TsaZCULOC8tBlWftDLC9Npark1B4FpVydcAnw.pdf',
            'vector_store_openai_id' => 'vs_e6vTaTUi0iJ1j0t7IEPZPXKf',
            'file_openai_id' => 'file-Q9lZOEzMDGU4ElScYCyzHvRa',
            'file_vector_openai_id' => 'file-Q9lZOEzMDGU4ElScYCyzHvRa',
            'content_file_openai_id' => 'BasedeConocimientoChatbotAsistenteparalaPlataformaLevely
                DescripcióndeLevelyLevelyesunsoftwarequetransformalagestióndelconocimientoen
                lasorganizaciones.Ofrecemosunaplataformaintuitivaqueempoderaacolegios,
                universidadesyempresasconherramientasavanzadasparalapersonalización,flexibilidad
                yestandarizacióndelaprendizaje.
                FuncionesdeLevely
                ●Creaciónygestióndecursospersonalizados:Losprofesorespuedencrearcursos
                desdecero,adaptarlosasuestructuradeenseñanzayhacerseguimientoasus
                alumnos.
                ●Gestióndeusuariosyroles:Administrartiposdeusuarios,crearaccesos,asignar
                rolesygenerardatosdeaprendizaje.
                ●Integracióndecontenidomultimedia:Incrustarvideos,presentaciones,archivosPDF,
                Word,Excelyotrosrecursosparaenriquecerlaexperienciadeaprendizaje.
                ●Comunicacióninterna:Chats,forosymensajesdirectosparaquetodoslosusuarios
                puedancomunicarsedentrodeLevely.
                ●Creacióndeexámenesyquizzes:Crearexámenes,quizzesylistadosdepreguntas
                rápidasparaevaluaralosestudiantesyanalizarsurendimiento.
                ServiciosadicionalesdeLevely
                ●Implementaciónpersonalizada:Solucionesadaptadasalasnecesidadesespecíficas
                decadacliente.
                ●Capacitaciónysoportecontinuo:Capacitaciónparaadministradoresyusuarios,
                ademásdesoportetécnicocontinuo.
                ●Actualizacionesconstantes:Mantenerlaplataformaactualizadaconlasúltimas
                mejorastecnológicas.
                ●Monetizaciónyalcance:Permitiralasempresaspublicarymonetizarsuscursosen
                laplataformaMOOC,ampliandosualcance.
                BeneficiosdeLevelyparalosestudiantes
                ●Actualizarseenelmundolaboralyuniversitario:Aprenderasuperarobstáculos
                inicialesyprepararsemejorparalauniversidadoelempleo.
                ●Enfocarseenconocimientosespecíficos:Medirelprogresoyaprenderdemanera
                efectivamedianteelsistemadeseguimiento.
                ●Desarrollarcapacidadesdeliderazgo:Potenciarnuevashabilidadesparaelfuturo
                profesionalconlaguíadelosmaestros.
                CaracterísticasybeneficiosdeLevelyLMS
                ●Gestióndeusuariosyseguimientodeprogreso:Facilitarlaadministraciónyel
                seguimientodelaprendizaje.
                ●Creacióndecontenidointeractivoyevaluacionespersonalizadas:Herramientas
                intuitivasypersonalizablesparaoptimizarlosprocesosdeenseñanza.
                ●Integraciónconplataformasexternasyanalíticasdetalladas:Conexiónconotros
                sistemasyanálisisdetalladodeldesempeño.
                ●Seguridaddedatos:Protocolosavanzadosycifradodeextremoaextremopara
                garantizarlaconfidencialidadeintegridaddelosdatos.
                ●Soportetécnicodedicado:Asistenciaenlacreacióndecontenidoyresoluciónde
                problemastécnicos,conrecursosenlíneaytutoriales.

                PlandeSuscripciónaLevely
                ●Modelodepago:Licenciasporusuario
                ●Costoporusuario:$5.000COPpormes
                ●Periodosdepago:Mensualesoanuales
                ●Ahorroanual:23%dedescuentoalpagaranualmente
                ●Condiciones:
                ○Pagoporcadausuariodelacompañíaqueuselaplataforma.
                ○Costode$5.000COPporusuario(posiblementesujetoacambios).
                Beneficiosdesuscripción
                ●Accesoalaplataformaparalosempleados
                ●Actualizacionesymejorasconstantes
                ●Soportetécnicoyatenciónalcliente
                Disponibilidad
                ●Puedessuscribirteencualquiermomentoyempezaradisfrutardelosbeneficiosde
                Levelydeinmediato.'
        ]);

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
