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
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
// use App\Traits\CosineSimilarityTrait;

class TalkMessageController extends Controller
{
    // use CosineSimilarityTrait;
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

        $tokenizer = new WhitespaceTokenizer();
        $vectorizer = new TokenCountVectorizer($tokenizer);

        $bestMatch = null;
        $bestSimilarity = -1;
        $bestSimilarText = 0;
        $bestLevenshtein = PHP_INT_MAX;
        $phrases = [];
        $normalizedPhrases = [];
        $intentMap = [];

        foreach ($intents as $intent) {
            foreach ($intent->trainingPhrases as $phrase) {
                $normalizedPhrase = $this->normalizeText($phrase->phrase);
                $normalizedPhrases[] = $normalizedPhrase;
                $phrases[] = $phrase->phrase;
                $intentMap[$phrase->phrase] = $intent;
            }
        }

        $normalizedMessage = $this->normalizeText($message);
        $phrasesSamples = [...$normalizedPhrases];
        $allSamples = array_merge([$normalizedMessage], $phrasesSamples);

        // $phrasesSamples = [...$phrases];
        // $allSamples = array_merge([$message], $phrasesSamples);

        $vectorizer->fit($allSamples);
        $vectorizer->transform($allSamples);
        $tfIdfTransformer = new TfIdfTransformer($allSamples);
        $tfIdfTransformer->transform($allSamples);

        $messageSample = $allSamples[0];
        $phraseVectors = array_slice($allSamples, 1);
        Log::info('Begin Comparing with message: ' . json_encode($messageSample));

        foreach ($phraseVectors as $i => $phraseVector) {
            $similarity = Distance::cosineSimilarity($messageSample, $phraseVector);
            similar_text($message, $phrases[$i], $percent);
            $levenshtein = levenshtein($message, $phrases[$i]);

            Log::info('Comparing with phrase: ' . $phrases[$i]);
            Log::info('phrase vector: ' . json_encode($phraseVector));
            Log::info('Cosine Similarity: ' . $similarity);
            Log::info('Similar Text Percent: ' . $percent);
            Log::info('Levenshtein Distance: ' . $levenshtein);

            if (
                $similarity > $bestSimilarity ||
                ($similarity == $bestSimilarity && $percent > $bestSimilarText) ||
                ($similarity == $bestSimilarity && $percent == $bestSimilarText && $levenshtein < $bestLevenshtein)
            ) {
                $bestSimilarity = $similarity;
                $bestSimilarText = $percent;
                $bestLevenshtein = $levenshtein;
                $bestMatch = $intentMap[$phrases[$i]];
                Log::info($phrases[$i] . ' ' . $percent . ' ' . $levenshtein . ' ' . $similarity);
            }
        }

        return $bestMatch;
    }

    private function normalizeText($text) {
        return strtolower(preg_replace('/[^a-z0-9\s]/', '', preg_replace('/\s+/', ' ', trim($text))));
    }


    // function CosineSimilarity($vector1, $vector2) {
        // [1,1,1,0,0]
        // [0,1,0,1,1]

        // Producto Punto = (1×0)+(1×1)+(1×0)+(0×1)+(0×1)
        // Producto Punto = 0+1+0+0+0 = 1

        // sqrt(1,1,1,0,0)
        // sqrt(3)
        // sqrt(0+1+0+0+0=1)
        // sqrt(3)
        // 1 / sqrt(3) x sqrt(3)

        // 1 / 3 = 0.3333

    //     $dotProduct = 0;
    //     $magnitude1 = 0;
    //     $magnitude2 = 0;
    //     foreach ($vector1 as $key => $value) {
    //         $dotProduct += $value * ($vector2[$key] ?? 0);
    //         $magnitude1 += $value * $value;
    //         $magnitude2 += ($vector2[$key] ?? 0) * ($vector2[$key] ?? 0);
    //     }
    //     $magnitude = sqrt($magnitude1) * sqrt($magnitude2);
    //     return $magnitude ? $dotProduct / $magnitude : 0;
    // }

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
