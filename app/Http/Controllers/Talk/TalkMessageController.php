<?php

namespace App\Http\Controllers\Talk;

use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\Talk\TalkMessage;
use App\Http\Controllers\Controller;
use App\Models\Intent\IntentResponse;
use App\Enums\TypeInformationRequired;
use App\Models\User\ContactInformation;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use App\Traits\CosineSimilarityTrait;

class TalkMessageController extends Controller
{
    use CosineSimilarityTrait;
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

        $response = $this->handleMessage($message, $chatbot->id, $intentId);

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

    protected function handleMessage($message, $chatbotId, $intentId)
    {
        $intent = $intentId ? Intent::find($intentId) : null;

        $matchedIntent = $this->findBestMatchIntent($message, $chatbotId);

        if ($matchedIntent) {
            $response = IntentResponse::where('intent_id', $matchedIntent->id)->inRandomOrder()->first();

            return $response ?? 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
        }

        if ($intent && $intent->save_information) {
            $response = $this->handleContactInformationSaving($message, $intent);
            if ($response) {
                return $response;
            }
        }

        return $response ?? 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
    }

    private function handleContactInformationSaving($message, Intent $intent)
    {
        if (in_array($intent->information_required, TypeInformationRequired::getValues(), true)) {
            $typeInformationRequired = TypeInformationRequired::from($intent->information_required);
            $pattern = $typeInformationRequired->getRegexPattern();

            if (preg_match($pattern, $message)) {
                ContactInformation::create([
                    'intent_id' => $intent->id,
                    'value' => $message
                ]);

                return 'Hemos guardado su información, un asesor se contactará con usted';
            } else {
                return 'La información proporcionada no coincide con el formato requerido, Por favor escribe solo la información solicitada sin ningún tipo de caracter especial.';
            }

            return false;
        }
    }

    private function findBestMatchIntent($message, $chatbotId)
    {
        $intents = Intent::where('chatbot_id', $chatbotId)->with('trainingPhrases')->get();
        $bestMatch = null;
        $bestSimilarity = -1;
        $bestSimilarText = 0;
        $bestLevenshtein = PHP_INT_MAX;

        $tokenizer = new WhitespaceTokenizer();
        $vectorizer = new TokenCountVectorizer($tokenizer);

        $phrases = [];
        $intentMap = [];
        foreach ($intents as $intent) {
            foreach ($intent->trainingPhrases as $phrase) {
                $phrases[] = $phrase->phrase;
                $intentMap[$phrase->phrase] = $intent;
            }
        }

        $vectorizer->fit($phrases);
        $vectorizer->transform($phrases);

        $messageVector = $vectorizer->transform([$message])[0];

        foreach ($phrases as $i => $phraseVector) {
            $similarity = $this->similarity($messageVector, $phraseVector);
            similar_text($message, $phrases[$i], $percent);
            $levenshtein = levenshtein($message, $phrases[$i]);

            if (
                $similarity > $bestSimilarity ||
                ($similarity == $bestSimilarity && $percent > $bestSimilarText) ||
                ($similarity == $bestSimilarity && $percent == $bestSimilarText && $levenshtein < $bestLevenshtein)
            ) {
                $bestSimilarity = $similarity;
                $bestSimilarText = $percent;
                $bestLevenshtein = $levenshtein;
                $bestMatch = $intentMap[$phrases[$i]];
            }
        }

        return $bestMatch;
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
