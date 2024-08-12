<?php

namespace App\Http\Controllers\Chatbot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotConfig;

class ChatbotConfigController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $chatbotId)
    {
        $request->validate([
            'messageColor' => 'required|string',
            'chatColor' => 'required|string',
        ]);

        $chatbotConfig = ChatbotConfig::where('chatbot_id', $chatbotId)->first();

        if ($chatbotConfig) {
            $chatbotConfig->update([
                'message_color' => $request->messageColor,
                'chat_color' => $request->chatColor,
            ]);
        } else {
            $chatbotConfig = ChatbotConfig::create([
                'chatbot_id' => $chatbotId,
                'message_color' => $request->messageColor,
                'chat_color' => $request->chatColor,
            ]);
        }

        return response()->json(['success' => true, 'data' => $chatbotConfig]);
    }
}
