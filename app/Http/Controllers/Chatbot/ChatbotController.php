<?php

namespace App\Http\Controllers\Chatbot;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Chatbot\Chatbot;
use App\Http\Controllers\Controller;

class ChatbotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:Reglas,PLN,Híbrido',
            'knowledge_base' => 'nullable|string',
            'link' => 'nullable|string|url',
        ]);

        $user = User::find(auth()->user()->id);

        $chatbot = Chatbot::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ]);

        if (isset($validatedData['knowledge_base']) || isset($validatedData['link'])) {
            $chatbot->knowledges()->create([
                'content' => $validatedData['knowledge_base'] ?? null,
                'link' => $validatedData['link'] ?? null,
            ]);
        }

        return response()->json(['saved' => true, 'chatbot' => $chatbot], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chatbot $chatbot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chatbot $chatbot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chatbot $chatbot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chatbot $chatbot)
    {
        //
    }
}
