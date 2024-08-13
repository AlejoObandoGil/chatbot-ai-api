<?php

namespace App\Http\Controllers\Chatbot;

use App\Models\User\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Chatbot\Chatbot;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatbotRequest;
use Illuminate\Support\Facades\Storage;

class ChatbotController extends Controller
{

    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chatbots = null;
        if (auth()->user())
            $chatbots = Chatbot::where('user_id', auth()->user()->id)->get();

        return response()->json(['chatbots' => $chatbots]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChatbotRequest  $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::find(auth()->user()->id);

            $chatbot = Chatbot::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'type' => $validatedData['type'],
                'temperature' => $request->input('temperature'),
                'max_tokens' => $request->input('maxTokens'),
            ]);

            if (isset($validatedData['knowledgeBase']) || isset($validatedData['link']) || $request->hasFile('document')) {
                $documentPath = null;

                if ($request->hasFile('document') && ($validatedData['type'] === 'Híbrido' || $validatedData['type'] === 'PLN')) {
                    // $file = $request->file('document');
                    $documentPath = $request->file('document')->store('documents', 'public');
                    $fileId = $this->openAIService->uploadFileGptApi($documentPath);
                    if ($fileId) {
                        $content_file_openai_id = $this->extractTextFromPdf($documentPath);
                    }

                    if ($fileId) {
                        $vectorStore = $this->openAIService->createVectorStore($validatedData['name'], $fileId);
                        $attempts = 0;
                        do {
                            sleep(2);
                            $vectorStore = $this->openAIService->retrieveVectorStore($vectorStore->id);
                            $attempts++;
                        } while ($vectorStore->status === 'in_progress' && $attempts < 10);

                        if ($vectorStore->status !== 'in_progress') {
                            $file_vector_openai_id = $this->openAIService->uploadFileVectorStore($fileId, $vectorStore->id);

                            if (!$chatbot->assistant_openai_id) {
                                $assistant = $this->openAIService->createAssistant($chatbot, $validatedData['knowledgeBase'], $vectorStore->id);
                                $chatbot->update([
                                    'assistant_openai_id' => $assistant->id,
                                ]);
                            }
                        } else {
                            Log::error('El vector store sigue en progreso después de 10 intentos');
                        }
                    }
                }
            }

            $chatbot->knowledges()->create([
                'content' => $validatedData['knowledgeBase'] ?? null,
                'link' => $validatedData['link'] ?? null,
                'document' => $documentPath ?? null,
                'vector_store_openai_id' => $vectorStore->id ?? null,
                'file_openai_id' => $fileId ?? null,
                'content_file_openai_id' => $content_file_openai_id ?? null,
                'file_vector_openai_id' => $file_vector_openai_id ?? null,
            ]);

            DB::commit();

            return response()->json(['message' => 'Chatbot guardado correctamente!', 'chatbot' => $chatbot->load('knowledges')], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar chatbot.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Chatbot $chatbot)
    {
        if (auth()->user())
            $chatbot = Chatbot::find($chatbot->id);

        return response()->json(['chatbot' => $chatbot]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chatbot $chatbot)
    {
        if (auth()->user()) {
            $chatbot = Chatbot::where('id', $chatbot->id)
                ->with(['config', 'knowledges' => function ($query) {
                    $query->where('is_learning', false)->first();
                }])
                ->first();
        }

        return response()->json(['chatbot' => $chatbot]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChatbotRequest $request, Chatbot $chatbot)
    {
        $validatedData = $request->validated();

        $chatbot->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
            'temperature' => $request->input('temperature'),
            'max_tokens' => $request->input('maxTokens'),
        ]);

        $knowledge = $chatbot->knowledges()->first();

        if ($request->hasFile('document') && ($validatedData['type'] === 'Híbrido' || $validatedData['type'] === 'PLN')) {
            if ($knowledge && $knowledge->document) {
                Storage::disk('public')->delete($knowledge->document);
            }
            $knowledge->document = $request->file('document')->store('documents', 'public');
            $knowledge->file_openai_id = $this->openAIService->uploadFileGptApi($knowledge->document);
            if ($knowledge->file_openai_id) {
                $knowledge->content_file_openai_id = $this->extractTextFromPdf($knowledge->document);
            }
        }

        if ($knowledge->file_openai_id) {
            $vectorStore = $this->openAIService->retrieveVectorStore($knowledge->vector_store_openai_id);
            if (!$knowledge->vector_store_openai_id) {
                if (!$vectorStore?->id) {
                    $vectorStore = $this->openAIService->createVectorStore($chatbot, $knowledge->file_openai_id);
                    $knowledge->vector_store_openai_id = $vectorStore->id;
                }
            }

            if ($vectorStore->status !== 'in_progress') {
                if (!$knowledge->file_vector_openai_id) {
                    $knowledge->file_vector_openai_id = $this->openAIService->uploadFileVectorStore($knowledge->file_openai_id, $knowledge->vector_store_openai_id);
                }
                if (!$chatbot->assistant_openai_id) {
                    $assistant = $this->openAIService->createAssistant($chatbot, $validatedData['knowledgeBase'], $vectorStore->id);
                    $chatbot->update([
                        'assistant_openai_id' => $assistant->id,
                    ]);
                } else {
                    $this->openAIService->modifyAssistant($chatbot, $validatedData['knowledgeBase'], $chatbot->assistant_openai_id, $knowledge->vector_store_openai_id);
                }
            }
        }

        $data = [
            'content' => $validatedData['knowledge_base'] ?? $knowledge->content,
            'link' => $validatedData['link'] ?? $knowledge->link,
            'document' => $knowledge->document ?? null,
            'vector_store_openai_id' => $knowledge->vector_store_openai_id ?? null,
            'file_openai_id' => $knowledge->file_openai_id ?? null,
            'file_vector_openai_id' => $knowledge->file_vector_openai_id ?? null,
            'content_file_openai_id' => $knowledge->content_file_openai_id ?? null,
        ];

        if ($knowledge) {
            $knowledge->update($data);
        } else {
            $chatbot->knowledges()->create($data);
        }

        return response()->json([
            'message' => 'Chatbot actualizado correctamente!',
            'chatbot' => $chatbot->load('knowledges'),
        ], 200);
    }

    public function updateEnable(Chatbot $chatbot)
    {
        $chatbot->enabled = !$chatbot->enabled;
        $chatbot->save();

        return response()->json([
            'chatbot' => $chatbot->load('knowledges'),
        ], 200);
    }

    private function extractTextFromPdf($filePath)
    {
        $fullPath = Storage::disk('public')->path($filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception("El archivo PDF no se encuentra en la ubicación esperada: {$fullPath}");
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);
        $text = $pdf->getText();

        return $text;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chatbot $chatbot)
    {
        //
    }
}
