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
    protected $intentMatcherService;

    public function __construct(ChatbotTalkProcessService $intentMatcherService)
    {
        $this->intentMatcherService = $intentMatcherService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Talk $talk)
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
    public function store(Request $request, Chatbot $chatbot, Talk $talk, $intentId = null)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:100',
        ]);

        $message = $validated['message'];

        $talk->messages()->create([
            // 'intent_id' => $intentId,
            'message' => $message,
            'sender' => 'user',
        ]);

        $response = $this->handleMessageProcess($message, $chatbot->id, $intentId, $talk);

        $talk->messages()->create([
            // 'intent_id' => $intentId,
            'message' => $response->response ?? $response,
            'sender' => 'bot',
        ]);

        if (is_object($response) && method_exists($response, 'load')) {
            $response = $response->load('intent');
        }

        return response()->json(['response' => $response]);
    }

    protected function handleMessageProcess($message, $chatbotId, $intentId, $talk)
    {
        $intent = $intentId ? Intent::find($intentId) : null;

        $matchedIntent = $this->intentMatcherService->findBestMatchIntent($message, $chatbotId);
        if ($matchedIntent) {
            $response = IntentResponse::where('intent_id', $matchedIntent->id)->inRandomOrder()->first();

            return $response ?? 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
        }

        if ($intent && $intent->save_information) {
            $response = $this->handleContactInformationSaving($message, $intent, $talk);
            if ($response) {
                return $response;
            }
        }

        return $response ?? 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
    }

    private function handleContactInformationSaving($message, Intent $intent, Talk $talk)
    {
        if (in_array($intent->information_required, TypeInformationRequired::getValues(), true)) {
            $typeInformationRequired = TypeInformationRequired::from($intent->information_required);
            $pattern = $typeInformationRequired->getRegexPattern();

            if (preg_match($pattern, $message)) {
                ContactInformation::create([
                    'intent_id' => $intent->id,
                    'talk_id' => $talk->id,
                    'value' => $message
                ]);

                return 'Hemos guardado su información, un asesor se contactará con usted';
            } else {
                return 'La información proporcionada no coincide con el formato requerido, Por favor escribe solo la información solicitada sin ningún tipo de caracter especial.';
            }

            return false;
        }
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


// protected function processMessage($message, $chatbotId, Talk $talk)
// {
//     $intent = $this->findIntent($message, $chatbotId);
//     if (!$intent) {
//         return 'Lo siento, no entendí su mensaje. ¿Puede reformular su pregunta?';
//     }

//     if ($intent->save_information) {
//         $collectedInfo = $talk->collected_information ?? [];
//         $nextInfoToCollect = $this->getNextInfoToCollect($intent->information_required, $collectedInfo);

//         if ($nextInfoToCollect) {
//             if ($this->validateInformation($message, $nextInfoToCollect)) {
//                 $this->saveInformation($talk, $intent->id, $nextInfoToCollect->value, $message);
//                 $collectedInfo[$nextInfoToCollect->value] = $message;
//                 $talk->collected_information = $collectedInfo;
//                 $talk->save();

//                 if (count($collectedInfo) < count($intent->information_required)) {
//                     $nextInfoToCollect = $this->getNextInfoToCollect($intent->information_required, $collectedInfo);
//                     return str_replace('{information_type}', $nextInfoToCollect->value, $intent->responses['request']);
//                 } else {
//                     return $intent->responses['success'];
//                 }
//             } else {
//                 return str_replace('{error_message}', $nextInfoToCollect->getErrorMessage(), $intent->responses['failure']);
//             }
//         }
//     }

//     return $intent->responses['initial'] ?? 'Gracias por su interés. ¿En qué puedo ayudarle?';
// }

// private function getNextInfoToCollect($requiredInfo, $collectedInfo)
// {
//     foreach ($requiredInfo as $info) {
//         if (!isset($collectedInfo[$info->value])) {
//             return $info;
//         }
//     }
//     return null;
// }

// private function validateInformation($message, TypeInformationRequired $infoType)
// {
//     return preg_match($infoType->getRegexPattern(), $message);
// }
