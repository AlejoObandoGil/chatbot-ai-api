<?php

namespace App\Http\Controllers\Intent;

use App\Models\Intent\Edge;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EdgeController extends Controller
{
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
    public function store(Request $request, Chatbot $chatbot)
    {
        $validatedData = $request->validate([
            'nodes' => 'required|array',
            'nodes.*.id' => 'required|uuid',
            'nodes.*.name' => 'required|string|max:191',
            'nodes.*.type' => 'required|string|max:191',
            'nodes.*.is_choice' => 'required|boolean',
            'nodes.*.position.x' => 'required|numeric',
            'nodes.*.position.y' => 'required|numeric',
            'nodes.*.data.label' => 'required|string|max:191',
            'nodes.*.category' => 'nullable|string',
            'nodes.*.save_information' => 'nullable|boolean',
            'nodes.*.information_required' => 'nullable|string',
            'edges' => 'nullable|array',
            'edges.*.id' => 'nullable|string',
            'edges.*.source' => 'nullable|uuid|exists:intents,id',
            'edges.*.sourceHandle' => 'nullable|string',
            'edges.*.target' => 'nullable|uuid|exists:intents,id',
        ]);

        DB::beginTransaction();

        try {
            $intents = $validatedData['nodes'];
            foreach ($intents as $intentData) {
                Intent::updateOrCreate(
                    ['id' => $intentData['id']],
                    [
                        'chatbot_id' => $chatbot->id,
                        'name' => $intentData['name'],
                        'type' => $intentData['type'],
                        'is_choice' => $intentData['is_choice'],
                        'position' => json_encode($intentData['position']),
                        'data' => json_encode($intentData['data']),
                        'category' => $intentData['category'] ?? null,
                        'save_information' => $intentData['save_information'] ?? null,
                        'information_required' => $intentData['information_required'] ?? null,
                    ]
                );
            }

            $edgeIdsInRequest = collect($validatedData['edges'])->pluck('id')->filter()->toArray();
            Edge::where('chatbot_id', $chatbot->id)->whereNotIn('id', $edgeIdsInRequest)->delete();

            $edges = $validatedData['edges'] ?? [];
            foreach ($edges as $edgeData) {
                Edge::updateOrCreate(
                    ['id' => $edgeData['id'] ?? null],
                    [
                        'chatbot_id' => $chatbot->id,
                        'source' => $edgeData['source'] ?? null,
                        'source_handle' => $edgeData['sourceHandle'] !== 'default' ? $edgeData['sourceHandle'] : null,
                        'target' => $edgeData['target'] ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json(['message' => 'Intenciones y conexiones guardadas correctamente!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar intenciones y conexiones.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Edge $edge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Edge $edge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Edge $edge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Edge $edge)
    {
        //
    }
}
