<?php

namespace App\Http\Controllers\Talk;

use Carbon\Carbon;
use App\Models\Talk\Talk;
use OpenAI\Resources\Chat;
use Illuminate\Http\Request;
use App\Models\Chatbot\Chatbot;
use App\Services\OpenAIService;
use App\Http\Controllers\Controller;

class TalkController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Chatbot $chatbot, Talk $talk)
    {
        $talk = $talk->load('messages');

        return response()->json(['chatbot' => $chatbot, 'talk' => $talk]);
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
                ->with('options');
        }])->find($chatbot->id);

        return response()->json(['chatbot' => $chatbot], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Chatbot $chatbot)
    {
        $thread = null;

        if ($chatbot->type === 'Híbrido' || $chatbot->type === 'PLN') {
            $thread = $this->openAIService->createThread();
        }

        $talk = Talk::create([
            'chatbot_id' => $chatbot->id,
            'thread_openai_id' => $thread ? $thread->id : null,
            'started_at' => Carbon::now()
        ]);

        return response()->json(['talkId' => $talk->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function close(Chatbot $chatbot, Talk $talk)
    {
        if ($talk->ended_at) {
            return response()->json(['talk' => $talk, 'closed' => true], 200);
        }

        $chatbot = Chatbot::find($talk->chatbot_id);
        if ($chatbot->type === 'Híbrido' || $chatbot->type === 'PLN') {
            if ($talk->thread_openai_id) {
                $thread = $this->openAIService->deleteThread($talk->thread_openai_id);
                $talk->update(['thread_deleted' => $thread->deleted]);
            }
        }
        $talk->update(['ended_at' => Carbon::now()]);

        return response()->json(['talk' => $talk, 'closed' => true], 200);
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
