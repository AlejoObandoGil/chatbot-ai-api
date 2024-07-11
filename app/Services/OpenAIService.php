<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Chatbot\Intent;
use App\Models\Chatbot\Chatbot;
use OpenAI\Resources\Completions;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use OpenAI\Responses\Completions\CreateResponse;

class OpenAIService
{
    public function __construct()
    {
        //
    }

    public function conexionGptApi($context = 'Eres un Asistente virtual de telefonia', $message = 'Cuales planes ofrecen ustedes')
    {
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $context . "\nUser: " . $message . "\nAI:"
                ],
            ],
            'temperature' => 0.2, // permitir al usaurio calibrar
            'max_tokens' => 10, // agregar que se pueda calcular el promedio de toikens que se vayan a usar segun la info de la empresa para ahorrar tokens
        ]);

        Log::info('$result api openAI: '.json_encode($result));

        return response()->json([
            'expected' => $result->choices[0]->message->content
        ]);
    }

    // public function handleMessage(Request $request)
    public function handleMessage(Request $request)
    {
        $userMessage = $request->input('message');
        $chatbotId = $request->input('chatbot_id');

        $chatbot = Chatbot::find($chatbotId);

        if (!$chatbot) {
            return response()->json(['error' => 'Chatbot no encontrado.'], 404);
        }

        $intent = Intent::where('chatbot_id', $chatbot->id)
            ->whereHas('trainingPhrases', function ($query) use ($userMessage) {
                $query->where('phrase', 'LIKE', "%$userMessage%");
            })
            ->first();

        if ($intent) {
            $response = $intent->responses->random()->response;
        } else {
            $context = $this->buildTrainingKnowledge($chatbot);

            Log::info('$context buil training knowledge: '.json_encode($context));

            $response = $this->conexionGptApi($context, $userMessage)['choices'][0]['text'];

            Log::info('$response text api openAI: '.json_encode($response));
        }

        return response()->json(['response' => $response]);
    }

    protected function buildTrainingKnowledge($chatbot)
    {
        $context = "Chatbot: " . $chatbot->name . "\nDescription: " . $chatbot->description . "\n";

        $intents = Intent::where('chatbot_id', $chatbot->id)->with('trainingPhrases', 'responses')->get();
        foreach ($intents as $intent) {
            $context .= "\nIntent: " . $intent->name;
            $context .= "\nTraining Phrases: ";
            foreach ($intent->trainingPhrases as $phrase) {
                $context .= $phrase->phrase . ", ";
            }
            $context .= "\nResponses: ";
            foreach ($intent->responses as $response) {
                $context .= $response->response . ", ";
            }
        }

        return $context;
    }

    // public function conexionGptApiTest($context = null, $message = null)
    // {
    //     OpenAI::fake([
    //         CreateResponse::fake([
    //             'choices' => [
    //                 [
    //                     'text' => 'awesome!',
    //                 ],
    //             ],
    //         ]),
    //     ]);

    //     $completion = OpenAI::completions()->create([
    //         'model' => 'gpt-3.5-turbo-instruct',
    //         'prompt' => 'PHP is ',
    //     ]);

    //     Log::info('$completion openai create: '.json_encode($completion));

    //     expect($completion['choices'][0]['text'])->toBe('awesome!');

    //     OpenAI::assertSent(Completions::class, function (string $method, array $parameters): bool {
    //         return $method === 'create' &&
    //             $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
    //             $parameters['prompt'] === 'PHP is ';
    //     });

    //     return response()->json([
    //         'expected' => $expected,
    //         'actual' => $actual,
    //     ]);
    // }
    // protected $client;
    // protected $apiKey;

    // public function __construct()
    // {
    //     $this->client = new Client();
    //     $this->apiKey = env('OPENAI_API_KEY');
    // }

    // public function conexionGptApiGuzz($context, $message)
    // {
    //     $response = $this->client->post('https://api.openai.com/v1/completions', [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $this->apiKey,
    //             'Content-Type' => 'application/json',
    //         ],
    //         'json' => [
    //             'model' => 'text-davinci-003',
    //             'prompt' => $context . "\nUser: " . $message . "\nAI:",
    //             'max_tokens' => 150,
    //             'temperature' => 0.7,
    //         ],
    //     ]);

    //     return json_decode($response->getBody(), true);
    // }
}
