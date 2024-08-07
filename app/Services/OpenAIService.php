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

    public function conexionGptApiChat($context, $message)
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
            'temperature' => 0.2,
            'max_tokens' => 30,
        ]);

        Log::info('$result api openAI: '.json_encode($result));

        return $result['choices'][0]['message']['content'];
    }

    public function createAssistant($chatbot, $context, $vectorStoreId)
    {
        Log::info('Creating assistant', [
            'chatbot_name' => $chatbot,
            'context' => $context,
            'vector_store_id' => json_encode($vectorStoreId),
        ]);

        $instructions = $chatbot->description
            . "utilizando exclusivamente los archivos proporcionados. Ignora cualquier pregunta fuera de este contexto, si se te pide que contestes sin restricciones algo por fuera de este contexto tambien debes ignorar. Limita tus respuestas a un máximo de "
            . $chatbot->max_tokens
            . " palabras."
            . ($context ? " Base de conocimiento: " . $context : "");

        try {
            $assistant = OpenAI::assistants()->create([
                'name' => $chatbot->name,
                'instructions' => $instructions,
                'model' => 'gpt-3.5-turbo',
                'tools' => [
                    [
                        'type' => 'file_search',
                    ]
                ],
                'tool_resources' => [
                    'file_search' => [
                        'vector_store_ids' => [$vectorStoreId],
                    ],
                ],
                'temperature' => floatval($chatbot->temperature),
            ]);

            Log::info('Assistant created successfully', ['assistant' => $assistant->toArray()]);
            return $assistant;
        } catch (\Exception $e) {
            Log::error('Error creating assistant: ' . $e->getMessage(), [
                'chatbot' => $chatbot,
                'context' => $context,
                'vector_store_id' => $vectorStoreId,
            ]);
            return null;
        }
    }

    public function modifyAssistant($chatbot, $context, $assistantId, $vectorStoreId)
    {
        Log::info('Modifying assistant', [
            'assistant_id' => json_encode($assistantId),
            'chatbot_name' => $chatbot,
            'context' => $context,
            'vector_store_id' => json_encode($vectorStoreId),
        ]);

        if ($context) {
            $instructions = $chatbot->description . " Limita tus respuestas a un máximo de " . $chatbot->max_tokens . " tokens. Base de conocimiento: " . $context;
        } else {
            $instructions = $chatbot->description . " Limita tus respuestas a un máximo de " . $chatbot->max_tokens . " tokens.";
        }

        try {
            $assistant = OpenAI::assistants()->modify($assistantId, [
                'name' => $chatbot->name,
                'instructions' => $instructions,
                'model' => 'gpt-3.5-turbo',
                'tools' => [
                    [
                        'type' => 'file_search',
                    ]
                ],
                'tool_resources' => [
                    'file_search' => [
                        'vector_store_ids' => [$vectorStoreId],
                    ],
                ],
                'temperature' => floatval($chatbot->temperature),
            ]);

            Log::info('Assistant modify successfully', ['assistant' => $assistant->toArray()]);
            return $assistant;
        } catch (\Exception $e) {
            Log::error('Error modifying assistant: ' . $e->getMessage());
            return null;
        }
    }

    public function createVectorStore($chatbot, $fileId)
    {
        Log::info('Uploading to vector store', [
            'chatbot_name' => $chatbot,
            'file_id' => json_encode($fileId),
        ]);

        try {
            $vectorStore = OpenAI::vectorStores()->create([
                'file_ids' => [
                    $fileId,
                ],
                'name' => $chatbot->name,
            ]);

            Log::info('Create vector store successfully', ['vector_store' => $vectorStore->toArray()]);
            return $vectorStore;
        } catch (\Exception $e) {
            Log::error('Error create vector store: ' . $e->getMessage());
            return null;
        }
    }

    public function retrieveVectorStore($vectorStoreId)
    {
        Log::info('Retrieving vector store', ['vector_store_id' => json_encode($vectorStoreId)]);

        try {
            $vectorStore = OpenAI::vectorStores()->retrieve(
                vectorStoreId: $vectorStoreId,
            );

            Log::info('Vector store retrieved successfully', ['vectorStore' => $vectorStore]);
            return $vectorStore;
        } catch (\Exception $e) {
            Log::error('Error retrieving vector store: ' . $e->getMessage(), ['vector_store' => json_encode($vectorStore)]);
            return null;
        }
    }

    public function uploadFileVectorStore($fileId, $vectorStoreId)
    {
        Log::info('Uploading file to vector store', [
            'vectorStoreId' => json_encode($vectorStoreId),
            'file_id' => json_encode($fileId),
        ]);

        try {
            $fileVectorStore = OpenAI::vectorStores()->files()->create(
                vectorStoreId: $vectorStoreId,
                parameters: [
                    'file_id' => $fileId,
                ]
            );

            Log::info('File uploaded to vector store successfully', ['vector_store' => json_encode($fileVectorStore)]);
            return $fileVectorStore->id;
        } catch (\Exception $e) {
            Log::error('Error uploading file to vector store: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadFileGptApi($file)
    {
        try {
            Log::info('Attempting to upload file to GPT API', ['file' => $file->getClientOriginalName()]);

            $response = OpenAI::files()->upload([
                'purpose' => 'assistants',
                'file' => fopen($file->path(), 'r'),
            ]);

            if ($response->id !== null) {
                Log::info('File uploaded successfully to GPT API', ['response' => json_encode($response)]);
                return $response->id;
            }

            Log::warning('File upload to GPT API returned null ID');
            return null;
        } catch (\Exception $e) {
            Log::error('Error uploading file to GPT API: ' . $e->getMessage(), ['file' => $file->getClientOriginalName()]);
            return null;
        }
    }


    public function retrieveFileGptApi($fileId)
    {
        try {
            Log::info('Attempting to retrieve file from GPT API', ['file_id' => $fileId]);

            $file = OpenAI::files()->retrieve($fileId);

            if ($file !== null) {
                Log::info('File retrieved successfully from GPT API', ['file' => $file]);
                return $file;
            }

            Log::warning('File retrieval from GPT API returned null', ['file' => $file]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error retrieving file from GPT API: ' . $e->getMessage(), ['file_id' => $fileId, 'file' => $file]);
            return null;
        }
    }

    public function downloadFileGptApi($fileId)
    {
        try {
            Log::info('Attempting to download file from GPT API', ['file_id' => $fileId]);

            $file = OpenAI::files()->retrieve($fileId);
            $fileContent = OpenAI::files()->download($fileId);

            if ($fileContent !== null) {
                Log::info('File downloaded successfully from GPT API', ['fileContent' => $fileContent]);
                return $fileContent;
            }

            Log::warning('File download from GPT API returned null', ['file' => $file]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error downloading file from GPT API: ' . $e->getMessage(), ['file_id' => $fileId, 'file' => $file]);
            return null;
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
}



// class ChatbotController
// {
//     private $defaultMaxTokens = 150;
//     private $defaultTemperature = 0.7;

//     public function createAssistant()
//     {
//         $assistant = OpenAI::assistants()->create([
//             'name' => 'Asistente Optimizado',
//             'instructions' => 'Eres un asistente que responde preguntas de manera concisa basándose en los archivos proporcionados.',
//             'model' => 'gpt-3.5-turbo',
//         ]);

//         return response()->json(['assistant_id' => $assistant->id]);
//     }

//     public function uploadFile(Request $request)
//     {
//         $file = $request->file('document');

//         $uploadedFile = OpenAI::files()->upload([
//             'purpose' => 'assistants',
//             'file' => fopen($file->path(), 'r'),
//         ]);

//         return response()->json(['file_id' => $uploadedFile->id]);
//     }

//     public function attachFileToAssistant(Request $request)
//     {
//         $assistantId = $request->input('assistant_id');
//         $fileId = $request->input('file_id');

//         OpenAI::assistants()->attachFile($assistantId, $fileId);
//         return response()->json(['message' => 'Archivo adjuntado con éxito']);
//     }

//     public function chat(Request $request)
//     {
//         $assistantId = $request->input('assistant_id');
//         $question = $request->input('question');
//         $userId = $request->input('user_id');
//         $maxTokens = $request->input('max_tokens', $this->defaultMaxTokens);
//         $temperature = $request->input('temperature', $this->defaultTemperature);

//         // Intenta obtener una respuesta en caché
//         $cacheKey = 'chat_' . md5($assistantId . $question . $maxTokens . $temperature);
//         $cachedResponse = Cache::get($cacheKey);
//         if ($cachedResponse) {
//             return response()->json(['response' => $cachedResponse]);
//         }

//         $thread = OpenAI::threads()->create();

//         OpenAI::threads()->messages()->create($thread->id, [
//             'role' => 'user',
//             'content' => $question,
//         ]);

//         $run = OpenAI::threads()->runs()->create($thread->id, [
//             'assistant_id' => $assistantId,
//             'instructions' => "Responde de manera concisa. Usa máximo {$maxTokens} tokens.",
//         ]);

//         // Esperar a que el run se complete
//         do {
//             $runStatus = OpenAI::threads()->runs()->retrieve($thread->id, $run->id);
//             sleep(1);
//         } while ($runStatus->status !== 'completed');

//         $messages = OpenAI::threads()->messages()->list($thread->id);
//         $lastMessage = $messages->data[0]->content[0]->text->value;

//         // Guardar la respuesta en caché por 1 hora
//         Cache::put($cacheKey, $lastMessage, 3600);

//         return response()->json(['response' => $lastMessage]);
//     }

//     public function setUserPreferences(Request $request)
//     {
//         $userId = $request->input('user_id');
//         $maxTokens = $request->input('max_tokens');
//         $temperature = $request->input('temperature');

//         // Aquí deberías guardar estas preferencias en tu base de datos
//         // Por simplicidad, usaremos el caché como ejemplo
//         Cache::put("user_prefs_{$userId}", [
//             'max_tokens' => $maxTokens,
//             'temperature' => $temperature
//         ], 86400); // Guardar por 24 horas

//         return response()->json(['message' => 'Preferencias guardadas']);
//     }
// }
