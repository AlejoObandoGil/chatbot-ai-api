<?php

namespace App\Http\Controllers\Entity;

use Illuminate\Http\Request;
use App\Models\Entity\Entity;
use App\Models\Chatbot\Chatbot;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chatbot $chatbot)
    {
        $entities = Entity::where('chatbot_id', $chatbot->id)->with(['values'])->get();

        Log::info($entities);

        return response()->json(['entities' => $entities]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entity $entity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        //
    }
}
