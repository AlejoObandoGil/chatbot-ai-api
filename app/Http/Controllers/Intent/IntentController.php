<?php

namespace App\Http\Controllers\Intent;

use App\Models\Intent\Edge;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use Illuminate\Support\Facades\DB;
use App\Models\Intent\IntentOption;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Intent\IntentResponse;
use App\Enums\TypeInformationRequired;
use App\Models\Intent\IntentTrainingPhrase;

class IntentController extends Controller
{
    /**
     * Display a listing of intents and edges for a specific chatbot.
     *
     * @param  \App\Models\Chatbot\Chatbot  $chatbot
     * @return \Illuminate\Http\JsonResponse.
     */
    public function index(Chatbot $chatbot)
    {
        $intents = Intent::where('chatbot_id', $chatbot->id)
            ->with(['responses', 'trainingPhrases', 'options'])
            ->get();

        $edgeIds = $intents->pluck('id');
        $edges = Edge::whereIn('source', $edgeIds)
            ->orWhereIn('target', $edgeIds)
            ->get();

        $enumValues = TypeInformationRequired::getValues();

        return response()->json([
            'intents' => $intents,
            'edges' => $edges,
            'type_information_required' => $enumValues,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Chatbot $chatbot)
    {
        $validatedData = $request->validate([
            'id' => 'required|uuid',
            'name' => 'required|string|max:191',
            'type' => 'required|string|max:191',
            'is_choice' => 'required|boolean',
            'position.x' => 'required|numeric',
            'position.y' => 'required|numeric',
            'data.label' => 'required|string|max:191',
            'category' => 'nullable|string',
            'save_information' => 'nullable|boolean',
            'information_required' => 'nullable|in:' . implode(',', TypeInformationRequired::getValues()),
            'training_phrases' => 'nullable|array',
            'training_phrases.*.id' => 'nullable|numeric',
            'training_phrases.*.phrase' => 'required|string|max:191',
            'responses' => 'nullable|array',
            'responses.*.id' => 'nullable|numeric',
            'responses.*.response' => 'required|string|max:191',
            'options' => 'array',
            'options.*.id' => 'required|uuid',
            'options.*.option' => 'string|max:191',
        ]);

        DB::beginTransaction();
        try {
            $intent = $this->updateOrCreateIntent($validatedData, $chatbot);

            $this->updateOrCreateTrainingPhrases($intent, $validatedData['training_phrases']);
            $this->updateOrCreateResponses($intent, $validatedData['responses']);
            $this->updateOrCreateOptions($intent, $validatedData['options']);

            DB::commit();

            return response()->json(['message' => 'Intención guardada correctamente!', 'intent' => $intent], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateOrCreateIntent(array $data, Chatbot $chatbot)
    {
        return Intent::updateOrCreate(
            ['id' => $data['id']],
            [
                'id' => $data['id'],
                'chatbot_id' => $chatbot->id,
                'name' => $data['name'],
                'is_choice' => $data['is_choice'] ?? false,
                'category' => $data['category'] ?? null,
                'save_information' => $data['save_information'] ?? false,
                'information_required' => $data['information_required'] ?? null,
                'position' => json_encode($data['position']),
                'data' => json_encode($data['data']),
                'type' => $data['type'] ?? 'customNode',
            ]
        );
    }

    private function updateOrCreateTrainingPhrases(Intent $intent, array $trainingPhrases)
    {
        $newPhrasesIds = Arr::pluck($trainingPhrases, 'id');
        foreach ($trainingPhrases as $phraseData) {
            $phraseId = $phraseData['id'] ?? null;
            $existingPhrase = IntentTrainingPhrase::where('intent_id', $intent->id)
            ->where('phrase', $phraseData['phrase'])
            ->first();

            if (!$phraseId && $existingPhrase) {
                continue;
            }
            IntentTrainingPhrase::updateOrCreate(
                ['id' => $phraseId],
                ['phrase' => $phraseData['phrase'], 'intent_id' => $intent->id]
            );
        }
        $intent->trainingPhrases()->whereNotIn('id', $newPhrasesIds)->delete();
    }

    private function updateOrCreateResponses(Intent $intent, array $responses)
    {
        $newResponsesIds = Arr::pluck($responses, 'id');
        foreach ($responses as $responseData) {
            $responseId = $responseData['id'] ?? null;
            $existingResponse = IntentResponse::where('intent_id', $intent->id)
                ->where('response', $responseData['response'])
                ->first();

            if (!$responseId && $existingResponse) {
                continue;
            }
            IntentResponse::updateOrCreate(
                ['id' => $responseId],
                ['response' => $responseData['response'], 'intent_id' => $intent->id]
            );
        }
        $intent->responses()->whereNotIn('id', $newResponsesIds)->delete();
    }

    private function updateOrCreateOptions(Intent $intent, array $options)
    {
        $newOptionsIds = Arr::pluck($options, 'id');
        foreach ($options as $optionData) {
            $optionId = $optionData['id'];
            $existingOption = IntentOption::where('intent_id', $intent->id)
            ->where('option', $optionData['option'])
            ->first();

            if (!$optionId && $existingOption) {
                continue;
            }
            IntentOption::updateOrCreate(
                ['id' => $optionId],
                ['option' => $optionData['option'], 'intent_id' => $intent->id]
            );
        }
        $intent->options()->whereNotIn('id', $newOptionsIds)->delete();
    }
}
