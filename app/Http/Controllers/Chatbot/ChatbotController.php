<?php

namespace App\Http\Controllers\Chatbot;

use App\Models\User\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Chatbot\Chatbot;
use App\Services\OpenAIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatbotRequest;
use Illuminate\Support\Facades\Storage;

class ChatbotController extends Controller
{

    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
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

        if (isset($validatedData['knowledge_base']) || isset($validatedData['link']) || $request->hasFile('document')) {
            $documentPath = null;
            $content = $validatedData['knowledge_base'] ?? null;

            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('documents', 'public');
                $content = $this->extractText($documentPath);
            }

            $chatbot->knowledge()->create([
                'content' => $content,
                'link' => $validatedData['link'] ?? null,
                'document' => $documentPath,
            ]);
        }

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Chatbot guardado correctamente!', 'chatbot' => $chatbot], 201);
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
        if (auth()->user())
            $chatbot = Chatbot::where('id', $chatbot->id)->with('knowledge')->first();

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

        $documentPath = null;
        $content = $validatedData['knowledge_base'] ?? null;

        if ($request->hasFile('document')) {
            // if ($chatbot->knowledge()->exists() && $chatbot->knowledges->document) {
            //     Storage::disk('public')->delete($chatbot->knowledges->document);
            // }

            $documentPath = $request->file('document')->store('documents', 'public');
            $documentPath = $this->OpenAIService->uploadFileGptApi($request->file('document'));
            // $content = $this->extractText($documentPath);
        }

        $knowledge = $chatbot->knowledge()->first();

        if ($knowledge) {
            $knowledge->update([
                'content' => $content,
                'link' => $validatedData['link'] ?? $knowledge->link,
                'document' => $documentPath ?? $knowledge->document,
            ]);
        } else {
            $chatbot->knowledge()->create([
                'content' => $content,
                'link' => $validatedData['link'] ?? null,
                'document' => $documentPath,
            ]);
        }

        return response()->json([
            'message' => 'Chatbot actualizado correctamente!',
            'chatbot' => $chatbot,
        ], 200);
    }

    public function updateEnable(Chatbot $chatbot)
    {
        $chatbot->enabled = !$chatbot->enabled;
        $chatbot->save();

        return response()->json([
            'chatbot' => $chatbot,
        ], 200);
    }

    private function extractText($filePath)
    {
        $filePath = storage_path('app/public/' . $filePath);
        // $parser = new Parser();
        $pdf = file_get_contents($filePath);
        return $pdf;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chatbot $chatbot)
    {
        //
    }
}
