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
     * @param  \App\Models\Chatbot  $chatbot
     * @return \Illuminate\Http\Response
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'training_phrases.*.phrase' => 'string|max:191',
            'responses' => 'nullable|array',
            'responses.*.id' => 'nullable|numeric',
            'responses.*.response' => 'nullable|string|max:191',
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
            IntentOption::updateOrCreate(
                ['id' => $optionId],
                ['option' => $optionData['option'], 'intent_id' => $intent->id]
            );
        }
        $intent->options()->whereNotIn('id', $newOptionsIds)->delete();
    }

    /**
     * Display the specified resource.
     */
    public function show(Intent $intent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intent $intent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intent $intent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intent $intent)
    {
        //
    }
}
