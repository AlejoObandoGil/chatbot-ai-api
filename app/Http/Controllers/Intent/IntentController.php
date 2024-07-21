<?php

namespace App\Http\Controllers\Intent;

use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Http\Controllers\Controller;
use App\Models\Intent\IntentResponse;
use App\Models\Intent\IntentTrainingPhrase;
use Illuminate\Support\Facades\Log;

class IntentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chatbot $chatbot)
    {
        $intents = Intent::where('chatbot_id', $chatbot->id)
            ->with(['responses', 'trainingPhrases', 'options'])
            ->get();

        Log::info($intents);

        return response()->json(['intents' => $intents]);
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
    public function store(Request $request, Chatbot $chatbot)
    {
        $intent = Intent::create([
            'chatbot_id' => $chatbot->id,
            'name' => $request->input('name'),
            'type_var' => $request->input('type_var'),
            'group' => $request->input('group'),
            'level' => $request->input('level'),
        ]);

        $trainingPhrase = $this->addTrainingPhrase($request, $intent->id);
        $intentResponse = $this->addIntentResponse($request, $intent->id);

        return response()->json($intent, 201);
    }

    public function addTrainingPhrase(Request $request, $intentId)
    {
        $trainingPhrase = IntentTrainingPhrase::create([
            'intent_id' => $intentId,
            'phrase' => $request->input('phrase'),
        ]);

        return $trainingPhrase;
    }

    public function addIntentResponse(Request $request, $intentId)
    {
        $intentResponse = IntentResponse::create([
            'intent_id' => $intentId,
            'response' => $request->input('response'),
        ]);

        return $intentResponse;
    }

    /**
     * Display the specified resource.
     */
    public function show(Intent $intent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intent $intent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intent $intent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intent $intent)
    {
        //
    }
}
