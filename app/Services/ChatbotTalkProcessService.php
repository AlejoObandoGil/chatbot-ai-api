<?php

namespace App\Services;

use App\Models\Intent\Intent;
use MathPHP\Statistics\Distance;
use Illuminate\Support\Facades\Log;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;

class ChatbotTalkProcessService
{
    public function findBestMatchIntent($message, $chatbotId)
    {
        $intents = Intent::where('chatbot_id', $chatbotId)->with('trainingPhrases')->get();

        $phrasesData = $this->extractPhrasesData($intents);

        $normalizedMessage = $this->normalizeText($message);
        $allSamples = $this->prepareSamples($normalizedMessage, $phrasesData['normalizedPhrases']);

        $messageSample = $allSamples[0];
        $phraseVectors = array_slice($allSamples, 1);

        $bestMatchData = $this->comparePhrases($message, $messageSample, $phraseVectors, $phrasesData['phrases'], $phrasesData['intentMap']);

        return $this->evaluateBestMatch($bestMatchData);
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
                $intentMap[$phrase->phrase] = $intent;
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

    private function comparePhrases($message, $messageSample, $phraseVectors, $phrases, $intentMap)
    {
        $bestMatch = null;
        $bestSimilarity = 0;
        $bestSimilarText = 0;
        $bestLevenshtein = PHP_INT_MAX;

        Log::info('Begin Comparing with message: ' . json_encode($messageSample));
        Log::info('Comparing Message: ' . json_encode($message));

        foreach ($phraseVectors as $i => $phraseVector) {
            $similarity = Distance::cosineSimilarity($messageSample, $phraseVector);
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
                Log::info($phrases[$i] . ' ' . $similarity. ' ' . $percent . ' ' . $levenshtein);
            }
        }

        return [
            'bestMatch' => $bestMatch,
            'bestSimilarity' => $bestSimilarity,
            'bestSimilarText' => $bestSimilarText,
            'bestLevenshtein' => $bestLevenshtein
        ];
    }

    private function evaluateBestMatch($bestMatchData)
    {
        $similarityThreshold = 0.3;
        $similarTextThreshold = 50;
        $levenshteinThreshold = 10;

        if (
            $bestMatchData['bestSimilarity'] < $similarityThreshold &&
            $bestMatchData['bestSimilarText'] < $similarTextThreshold &&
            $bestMatchData['bestLevenshtein'] >= $levenshteinThreshold
        ) {
            Log::info('Sin coincidencia');
            return null;
        }

        Log::info('bestMatch: ', ['bestMatch' => json_encode($bestMatchData['bestMatch'])]);

        return $bestMatchData['bestMatch'];
    }

    private function normalizeText($text)
    {
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
}
