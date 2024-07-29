<?php

namespace App\Http\Controllers\Talk;

use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\Talk\TalkMessage;
use MathPHP\Statistics\Distance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Intent\IntentResponse;
use App\Enums\TypeInformationRequired;
use App\Models\User\ContactInformation;
use App\Services\ChatbotTalkProcessService;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
// use App\Traits\CosineSimilarityTrait;

class TalkMessageController extends Controller
{
    protected $chatbotTalkProcessService;

    public function __construct(ChatbotTalkProcessService $chatbotTalkProcessService)
    {
        $this->chatbotTalkProcessService = $chatbotTalkProcessService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Chatbot $chatbot, Talk $talk, $intent = null)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:100',
        ]);

        $message = $validated['message'];

        $talk->messages()->create([
            'intent_id' => $intent !== 'false' ? $intent : null,
            'message' => $message,
            'sender' => 'user',
        ]);

        $response = $this->chatbotTalkProcessService->handleMessageProcess($message, $chatbot->id, $intent, $talk);

        $talk->messages()->create([
            'intent_id' => $intent !== 'false' ? $intent : null,
            'message' => $response->response ?? $response,
            'sender' => 'bot',
        ]);

        if (is_object($response) && method_exists($response, 'load')) {
            $response = $response->load('intent');
        }

        return response()->json(['response' => $response]);
    }
}
