<?php

namespace App\Http\Controllers;

use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\Talk\TalkMessage;
use App\Models\Intent\IntentResponse;

class TalkMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Chatbot $chatbot, Talk $talk)
    {
        $message = $request->input('message');

        $talkMessage = TalkMessage::create([
            'talk_id' => $talk->id,
            'message' => $message,
            'sender' => 'user',
        ]);

        $response = $this->processMessage($message, $chatbot->id);

        TalkMessage::create([
            'talk_id' => $talk->id,
            'message' => $response,
            'sender' => 'bot',
        ]);

        return response()->json(['response' => $response]);
    }

    protected function processMessage($message, $chatbotId)
    {
        $intent = Intent::where('chatbot_id', $chatbotId)
            ->whereHas('trainingPhrases', function($query) use ($message) {
                $query->where('phrase', 'like', '%' . $message . '%');
            })->first();

        if ($intent) {
            $response = IntentResponse::where('intent_id', $intent->id)->inRandomOrder()->first();
            return $response ? $response->response : 'Lo siento, no entendí tu mensaje.';
        }

        return 'Lo siento, no entendí tu mensaje.';
    }

    /**
     * Display the specified resource.
     */
    public function show(TalkMessage $talkMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TalkMessage $talkMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TalkMessage $talkMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TalkMessage $talkMessage)
    {
        //
    }
}
