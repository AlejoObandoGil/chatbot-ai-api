<?php

namespace App\Services;

use App\Models\Talk\Talk;
use App\Models\Intent\Edge;
use App\Models\Intent\Intent;
use MathPHP\Statistics\Distance;
use App\Models\Intent\IntentOption;
use Illuminate\Support\Facades\Log;
use App\Models\Intent\IntentResponse;
use App\Enums\TypeInformationRequired;
use App\Models\User\ContactInformation;
use App\Models\Intent\IntentTrainingPhrase;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;

class ChatbotTalkProcessService
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function handleMessageProcess($message, $option, $chatbot, $intentId, $talk)
    {
        $intent = $intentId ? Intent::find($intentId) : null;

        Log::info('handleMessageProcess');

        if ($option['is_option']) {
            $responseOption = $this->handleOption($option);
            if ($responseOption) {
                return $responseOption;
            }
        }

        $matchedIntent = $this->findBestMatchIntent($message, $chatbot, $intentId);
        if ($matchedIntent['answer'] && isset($matchedIntent['bestMatchData'])) {
            $response = IntentResponse::where('intent_id', $matchedIntent['bestMatchData']['id'])->inRandomOrder()->first();

            return $response ?? 'Lo siento, no tengo una respuesta para esto, por favor intenta preguntar de otra forma.';
        }

        if (($chatbot->type === 'Híbrido' || $chatbot->type === 'PLN') && (!$intent || !$intent->save_information)) {
            $prepareInstruction = null;
            if (isset($matchedIntent['bestMatchData'])) {
                $prepareInstruction = $this->prepareInstructions($matchedIntent['bestMatchData']);
            }
            if ($talk->thread_openai_id) {
                $response = $this->openAIService->createMessage($talk->thread_openai_id, $chatbot, $message, $prepareInstruction);

                return $response ?? 'Lo siento, no tengo una respuesta para esto, por favor intenta preguntar de otra forma.';
            }
        }

        if ($intent && $intent->save_information) {
            $response = $this->handleContactInformationSaving($message, $intent, $talk);
            if ($response) {
                return $response;
            }
        }

        return $response ?? 'Lo siento, no tengo una respuesta para esto, por favor intenta preguntar de otra forma.';
    }

    public function prepareInstructions($bestMatchData)
    {
        Log::info('prepareInstructions');

        $intents = Intent::where('id', $bestMatchData->id)->with('trainingPhrases', 'responses')->get();

        $instructions = "Contexto: ";
        $intentNames = [];
        $trainingPhrases = [];
        $responses = [];

        foreach ($intents as $intent) {
            $intentNames[] = $intent->name;
            foreach ($intent->trainingPhrases as $phrase) {
                $trainingPhrases[] = $phrase->phrase;
            }
            foreach ($intent->responses as $response) {
                $responses[] = $response->response;
            }
        }

        $instructions .= "Intenciones: " . implode(", ", $intentNames) . ". ";
        $instructions .= "Frases de Entrenamiento: " . implode(", ", $trainingPhrases) . ". ";
        $instructions .= "Respuestas: " . implode(", ", $responses) . ". ";

        return $instructions;
    }

    private function handleOption($option)
    {
        Log::info('handleOption');

        $edgeOptionFind = Edge::where('source_handle', $option['id'])->first();
        $defaultResponse = null;
        if ($edgeOptionFind) {
            $intentTargetResponse = $edgeOptionFind->targetIntent->responses()->inRandomOrder()->first();
            return $intentTargetResponse ?? $defaultResponse;
        }
        return $defaultResponse;
    }

    public function handleContactInformationSaving($message, Intent $intent, Talk $talk)
    {
        Log::info('handleContactInformationSaving');

        if (in_array($intent->information_required, TypeInformationRequired::getValues(), true)) {
            $typeInformationRequired = TypeInformationRequired::from($intent->information_required);
            $pattern = $typeInformationRequired->getRegexPattern();

            if (preg_match($pattern, $message)) {
                ContactInformation::create([
                    'intent_id' => $intent->id,
                    'talk_id' => $talk->id,
                    'value' => $message
                ]);
                $edgeIntentFind = Edge::where('source', $intent->id)->first();
                $defaultResponse  = 'Hemos guardado su información, un asesor se contactará con usted';
                if ($edgeIntentFind) {
                    $intentTargetResponse = $edgeIntentFind->targetIntent->responses()->inRandomOrder()->first();
                    return $intentTargetResponse ?? $defaultResponse;
                }
                return $defaultResponse;
            } else {
                return $typeInformationRequired->getErrorMessage();
            }

            return false;
        }
    }

    public function findBestMatchIntent($message, $chatbot, $intentId)
    {
        Log::info('findBestMatchIntent');

        $intents = Intent::where('chatbot_id', $chatbot->id)->with('trainingPhrases')->get();

        $phrasesData = $this->extractPhrasesData($intents);

        $normalizedMessage = $this->normalizeText($message);
        $allSamples = $this->prepareSamples($normalizedMessage, $phrasesData['normalizedPhrases']);

        $messageSample = $allSamples[0];
        $phraseVectors = array_slice($allSamples, 1);

        $bestMatchData = $this->comparePhrases($normalizedMessage, $messageSample, $phraseVectors, $phrasesData['normalizedPhrases'], $phrasesData['intentMap']);

        return $this->evaluateBestMatch($bestMatchData, $normalizedMessage, $message, $chatbot, $intentId);
    }

    private function extractPhrasesData($intents)
    {
        Log::info('extractPhrasesData');

        $phrases = [];
        $normalizedPhrases = [];
        $intentMap = [];

        foreach ($intents as $intent) {
            foreach ($intent->trainingPhrases as $phrase) {
                $normalizedPhrase = $this->normalizeText($phrase->phrase);
                $normalizedPhrases[] = $normalizedPhrase;
                $phrases[] = $phrase->phrase;
                $intentMap[$normalizedPhrase] = $intent;
            }
        }

        return [
            'phrases' => $phrases,
            'normalizedPhrases' => $normalizedPhrases,
            'intentMap' => $intentMap
        ];
    }

    private function prepareSamples($normalizedMessage, $normalizedPhrases)
    {
        Log::info('prepareSamples');

        $tokenizer = new WhitespaceTokenizer();
        $vectorizer = new TokenCountVectorizer($tokenizer);

        $allSamples = array_merge([$normalizedMessage], $normalizedPhrases);

        $vectorizer->fit($allSamples);
        $vectorizer->transform($allSamples);

        $tfIdfTransformer = new TfIdfTransformer($allSamples);
        $tfIdfTransformer->transform($allSamples);

        return $allSamples;
    }

    private function comparePhrases($normalizedMessage, $messageSample, $phraseVectors, $normalizedPhrases, $intentMap)
    {
        Log::info('comparePhrases');

        $bestMatch = null;
        $messageLength = str_word_count($normalizedMessage);

        if ($messageLength <= 3) {
            $bestCosineSimilarity = 0.1;
            $bestSimilarText = 60;
            $bestLevenshtein = 5;
        } elseif ($messageLength > 3 && $messageLength <= 7) {
            $bestCosineSimilarity = 0.2;
            $bestSimilarText = 55;
            $bestLevenshtein = 7;
        } else {
            $bestCosineSimilarity = 0.2;
            $bestSimilarText = 40;
            $bestLevenshtein = 9;
        }

        Log::info('Begin Comparing Message + word count: ' . json_encode($normalizedMessage) . ' ' . json_encode($messageLength));

        foreach ($phraseVectors as $i => $phraseVector) {
            $cosineSimilarity = Distance::cosineSimilarity($messageSample, $phraseVector);
            similar_text($normalizedMessage, $normalizedPhrases[$i], $percent);
            $levenshtein = levenshtein($normalizedMessage, $normalizedPhrases[$i]);

            if (
                ($cosineSimilarity > $bestCosineSimilarity && $percent >= $bestSimilarText) ||
                ($percent >= $bestSimilarText && $levenshtein < $bestLevenshtein) ||
                ($cosineSimilarity >= $bestCosineSimilarity && $percent >= $bestSimilarText && $levenshtein < $bestLevenshtein)
            ) {
                $bestCosineSimilarity = $cosineSimilarity;
                $bestSimilarText = $percent;
                $bestLevenshtein = $levenshtein;
                $bestMatch = $intentMap[$normalizedPhrases[$i]];
                Log::info('Training Phrase: ' . $bestMatch . $normalizedPhrases[$i] . ', ' . $cosineSimilarity. ', ' . $percent . ', ' . $levenshtein);
            }
        }

        return [
            'bestMatch' => $bestMatch,
            'bestCosineSimilarity' => $bestCosineSimilarity,
            'bestSimilarText' => $bestSimilarText,
            'bestLevenshtein' => $bestLevenshtein
        ];
    }

    private function evaluateBestMatch($bestMatchData, $normalizedMessage, $userMessage, $chatbot, $intentId)
    {
        Log::info('Best Match:', ['bestMatch' => $bestMatchData['bestMatch']]);

        if (!isset($bestMatchData['bestMatch'])) {
            Log::info('No match');

            return [
                'bestMatchData' => $bestMatchData['bestMatch'],
                'answer' => false
            ];
        }

        $messageLength = str_word_count($userMessage);

        if ($messageLength <= 3) {
            $cosineWeight = 0.1;
            $similarTextWeight = 0.5;
            $levenshteinWeight = 0.4;
        } elseif ($messageLength > 3 && $messageLength <= 7) {
            $cosineWeight = 0.2;
            $similarTextWeight = 0.55;
            $levenshteinWeight = 0.25;
        } else {
            $cosineWeight = 0.25;
            $similarTextWeight = 0.55;
            $levenshteinWeight = 0.2;
        }

        $cosineSimilarityScore = $bestMatchData['bestCosineSimilarity'];
        $similarTextScore = $bestMatchData['bestSimilarText'] / 100;
        $levenshteinScore = 1 - ($bestMatchData['bestLevenshtein'] / max(1, strlen($normalizedMessage)));
        // 1 - (2) / max(1, 10) = 1 - (2) / 10 = 1 - 0.2 = 0.8

        $weightedScore = (
            $cosineWeight * $cosineSimilarityScore +
            $similarTextWeight * $similarTextScore +
            $levenshteinWeight * $levenshteinScore
        );

        Log::info('Best match normalized score: ' . $cosineSimilarityScore . ', ' . $similarTextScore . ', ' . $levenshteinScore . ', ' . $weightedScore);

        if (($weightedScore >= 0.6 && $chatbot->type === 'Basado en reglas' && $bestMatchData['bestMatch']['save_information'])
            || ($weightedScore >= 0.5 && $chatbot->type === 'Basado en reglas' && !$bestMatchData['bestMatch']['save_information'])
            || ($weightedScore >= 0.6 && $chatbot->type === 'Híbrido')
        ) {
            $this->hanldeTrainingPhrasesSaving($chatbot->id, $bestMatchData['bestMatch'], $userMessage);

            Log::info('$weightedScore >= 0.5: ' . json_encode($bestMatchData['bestMatch']));

            return [
                'bestMatchData' => $bestMatchData['bestMatch'],
                'answer' => true
            ];
        }

        if (!$bestMatchData['bestMatch']['save_information']) {
            return [
                'bestMatchData' => $bestMatchData['bestMatch'],
                'answer' => false
            ];
        }

        Log::info('No match');

        return [
            'answer' => false
        ];
    }

    public function hanldeTrainingPhrasesSaving($chatbotId, $bestMatch, $userMessage)
    {
        Log::info('hanldeTrainingPhrasesSaving');

        $intent = Intent::where('chatbot_id', $chatbotId)
            ->whereHas('trainingPhrases', function($query) use ($userMessage) {
                $query->where('phrase', 'like', '%' . $userMessage . '%');
            })->first();

        Log::info('Find Intent phrase: ' . $intent);

        if (is_null($intent) && $bestMatch) {
            IntentTrainingPhrase::create([
                'intent_id' => $bestMatch['id'],
                'phrase' => $userMessage,
                'is_learning' => true
            ]);

            Log::info('Save Intent phrase: ' . $bestMatch['id']);
        }
    }

    /**
     * Normalizes text by converting it to lowercase, removing extra whitespace,
     * and stripping out non-alphanumeric characters except for spaces and 'H'.
     *
     * @param string $text The text to normalize.
     * @return string The normalized text.
     */
    private function normalizeText($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/\s+/', ' ', trim($text));
        $text = preg_replace('/[^a-z0-9\sH]/', '', $text);

        return $text;
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
}
