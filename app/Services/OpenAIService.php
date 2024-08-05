<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\Chatbot\Knowledge;
use OpenAI\Resources\Completions;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Models\Learning\LearningKnowledge;
use OpenAI\Responses\Completions\CreateResponse;

class OpenAIService
{
    public function __construct()
    {
        //
    }

    public function conexionGptApi($context, $message)
    {
        $content = $context . "User:" . $message. ". " . "AI:";

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content
                ],
            ],
            'temperature' => 0.2, // permitir al usuario calibrar
            'max_tokens' => 30, // agregar que se pueda calcular el promedio de toikens que se vayan a usar segun la info de la empresa para ahorrar tokens
        ]);

        Log::info('$result api openAI: '.json_encode($result));

        return $result['choices'][0]['message']['content'];
    }

    public function uploadFileGptApi($file)
    {
        try {
            $response = OpenAI::files()->upload([
                'purpose' => 'assistants',
                'file' => fopen($file->path(), 'r'),
            ]);

            if ($response->id === null) {
                $fileId = $response->id;
                return $fileId;
                // $file = OpenAI::files()->retrieve($fileId);
                // $fileContent = OpenAI::files()->download($fileId);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return response()->json(['error' => 'Error uploading file'], 500);
        }
    }
    // public function conexionGptApiTest($context = null, $message = null)
    // {
    //     $content = $context . "User:" . $message . ". " . "AI:";

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

    //     Log::info('$completion openai create: ' . json_encode($completion));

    //     if ($completion['choices'][0]['text'] !== 'awesome!') {
    //         throw new \Exception('Error: El texto de la respuesta no es el esperado.');
    //     }

    //     OpenAI::assertSent(Completions::class, function (string $method, array $parameters): bool {
    //         return $method === 'create' &&
    //             $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
    //             $parameters['prompt'] === 'PHP is ';
    //     });

    //     return $completion['choices'][0]['text'];
    // }


    // public function handleMessage(Request $request)
    public function handleMessage()
    {
        // $userMessage = $request->input('message');
        // $chatbotId = $request->input('chatbot_id');
        $chatbotId = 1;
        $userMessage = 'Cuales planes de internet tienen';

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
            $context = $this->buildLearningKnowledge($chatbot);

            Log::info('$context buil training knowledge: '.json_encode($context));

            $response = $this->conexionGptApi($context, $userMessage);
            // $response = $this->conexionGptApiTest($context, $userMessage);

            Log::info('$response text api openAI: '.json_encode($response));
        }

        return response()->json(['response' => $response]);
    }

    protected function buildLearningKnowledge($chatbot)
    {
        $context = "Chatbot:" . $chatbot->name. ". " . "Descripción:" . $chatbot->description . "Eres un agente, ayuda a los clientes. Si no tienes respuesta, responder Lo siento, no tengo una respuesta para eso.";

        $intents = Intent::where('chatbot_id', $chatbot->id)->with('trainingPhrases', 'responses')->get();

        $intentNames = [];
        $trainingPhrases = [];
        $responses = [];

        foreach ($intents as $intent) {
            $intentNames[] = $intent->name;
            foreach ($intent->trainingPhrases as $phrase) {
                $trainingPhrases[] = $phrase->phrase;
            }
            foreach ($intent->responses as $response) {
                $responses[] = $response->response;
            }
        }

        $context .= "Intenciones: " . implode(", ", $intentNames) . ". ";
        $context .= "Frases de Entrenamiento: " . implode(", ", $trainingPhrases) . ". ";
        $context .= "Respuestas: " . implode(", ", $responses) . ". ";

        return $this->createLearningKnowledge($chatbot, $context);
    }

    public function createLearningKnowledge($chatbot, $context)
    {
        $learningKnowledge = Knowledge::where('chatbot_id', $chatbot->id)->first();
        if (!$learningKnowledge) {
            $learningKnowledge = new Knowledge();
            $learningKnowledge->chatbot_id = $chatbot->id;
            $learningKnowledge->content = $context;
            $learningKnowledge->is_learning = true;
            $learningKnowledge->save();
        }

        return $context;
    }

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
