<?php

namespace Tests\Feature;

use Tests\TestCase;
use OpenAI\Resources\Completions;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use OpenAI\Responses\Completions\CreateResponse;

class OpenAiApiTest extends TestCase
{
    public function testConexionGptApi()
    {
        // Configura la simulación de la respuesta de la API
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'text' => 'awesome!',
                    ],
                ],
            ]),
        ]);

        // Envía una solicitud de completación
        $completion = OpenAI::completions()->create([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => 'PHP is ',
        ]);

        Log::info('$completion openai create: '.json_encode($completion));


        // Verifica que la respuesta recibida sea 'awesome!'
        $this->assertEquals('awesome!', $completion['choices'][0]['text']);

        // Asegúrate de que la solicitud de completación se haya enviado con los parámetros correctos
        OpenAI::assertSent(Completions::class, function (string $method, array $parameters): bool {
            return $method === 'create' &&
                $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
                $parameters['prompt'] === 'PHP is ';
        });
    }
}


