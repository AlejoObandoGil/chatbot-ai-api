<?php

namespace App\Http\Controllers\Talk;

use Carbon\Carbon;
use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Chatbot\Chatbot;
use App\Http\Controllers\Controller;
use OpenAI\Resources\Chat;

class TalkController extends Controller
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
    public function create(Chatbot $chatbot)
    {
        $chatbot = Chatbot::with(['intents' => function ($query) {
            $query->where('category', 'saludo')
                ->with(['responses' => function ($responseQuery) {
                    $responseQuery->inRandomOrder()->limit(1);
                }])
                ->first();
        }])->find($chatbot->id);

        return response()->json(['chatbot' => $chatbot], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Chatbot $chatbot)
    {
        $talk = Talk::create([
            'chatbot_id' => $chatbot->id,
            'started_at' => Carbon::now()
        ]);

        return response()->json(['talkId' => $talk->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Talk $talk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Talk $talk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Talk $talk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Talk $talk)
    {
        //
    }
}
