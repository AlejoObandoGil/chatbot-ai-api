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
    public function handleMessageProcess($message, $option, $chatbotId, $intentId, $talk)
    {
        $intent = $intentId ? Intent::find($intentId) : null;

        Log::info($intent);

        if ($option['is_option']) {
            $optionFind = Edge::where('source_handle', $option['id'])->first();
            $intentTargetResponse = null;
            if ($optionFind) {
                $intentTargetResponse = IntentResponse::where('intent_id', $optionFind->target)->inRandomOrder()->first();
                return $intentTargetResponse ?? 'Lo siento, parece qeu aun no hay una respuesta disponible.';
            }
            return $intentTargetResponse;
        }

        $matchedIntent = $this->findBestMatchIntent($message, $chatbotId, $intentId);
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

    public function handleContactInformationSaving($message, Intent $intent, Talk $talk)
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

    public function findBestMatchIntent($message, $chatbotId, $intentId)
    {
        $intents = Intent::where('chatbot_id', $chatbotId)->with('trainingPhrases')->get();

        $phrasesData = $this->extractPhrasesData($intents);

        $normalizedMessage = $this->normalizeText($message);
        $allSamples = $this->prepareSamples($normalizedMessage, $phrasesData['normalizedPhrases']);

        $messageSample = $allSamples[0];
        $phraseVectors = array_slice($allSamples, 1);

        $bestMatchData = $this->comparePhrases($normalizedMessage, $messageSample, $phraseVectors, $phrasesData['normalizedPhrases'], $phrasesData['intentMap']);

        return $this->evaluateBestMatch($bestMatchData, $normalizedMessage, $message, $chatbotId, $intentId);
    }

    private function extractPhrasesData($intents)
    {
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
        $bestMatch = null;
        $messageLength = str_word_count($normalizedMessage);

        if ($messageLength <= 2) {
            $bestCosineSimilarity = 0.1;
            $bestSimilarText = 50;
            $bestLevenshtein = 5;
        } elseif ($messageLength <= 5) {
            $bestCosineSimilarity = 0.1;
            $bestSimilarText = 45;
            $bestLevenshtein = 6;
        } else {
            $bestCosineSimilarity = 0.2;
            $bestSimilarText = 40;
            $bestLevenshtein = 8;
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
                Log::info('Training Phrase: ' . $normalizedPhrases[$i] . ', ' . $cosineSimilarity. ', ' . $percent . ', ' . $levenshtein);
            }
        }

        return [
            'bestMatch' => $bestMatch,
            'bestCosineSimilarity' => $bestCosineSimilarity,
            'bestSimilarText' => $bestSimilarText,
            'bestLevenshtein' => $bestLevenshtein
        ];
    }

    private function evaluateBestMatch($bestMatchData, $normalizedMessage, $userMessage, $chatbotId, $intentId)
    {
        $messageLength = str_word_count($userMessage);

        if ($messageLength <= 2) {
            $cosineWeight = 0.1;
            $similarTextWeight = 0.4;
            $levenshteinWeight = 0.5;
        } elseif ($messageLength <= 5) {
            $cosineWeight = 0.2;
            $similarTextWeight = 0.65;
            $levenshteinWeight = 0.15;
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

        if ($weightedScore >= 0.5) {
            $this->hanldeTrainingPhrasesSaving($chatbotId, $bestMatchData['bestMatch'], $userMessage);

            return $bestMatchData['bestMatch'];
        }

        Log::info('No match');

        return null;
    }

    public function hanldeTrainingPhrasesSaving($chatbotId, $bestMatch, $userMessage)
    {
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
