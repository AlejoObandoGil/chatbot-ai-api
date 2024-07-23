<?php

namespace App\Http\Controllers\Talk;

use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Models\Talk\TalkMessage;
use App\Http\Controllers\Controller;
use App\Models\Intent\IntentResponse;
use App\Models\User\ContactInformation;

class TalkMessageController extends Controller
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
    public function store(Request $request, Chatbot $chatbot, Talk $talk, $intentId = null)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:100',
        ]);

        $message = $validated['message'];

        $talk->messages()->create([
            'intentId' => $intentId,
            'message' => $message,
            'sender' => 'user',
        ]);

        $response = $this->processMessage($message, $chatbot->id);

        $talk->messages()->create([
            'intentId' => $intentId,
            'message' => $response->response ?? $response,
            'sender' => 'bot',
        ]);

        if ($intentId) {
            $intent = Intent::find($intentId);
            if ($intent && $intent->save_information) {
                ContactInformation::create([
                    'intent_id' => $intent->id,
                    'value' => $message
                ]);
            }
        }

        return response()->json(['response' => $response->load('intent')]);
    }

    protected function processMessage($message, $chatbotId)
    {
        $intent = Intent::where('chatbot_id', $chatbotId)
            ->whereHas('trainingPhrases', function($query) use ($message) {
                $query->where('phrase', 'like', '%' . $message . '%');
            })->first();

        if ($intent) {
            $response = IntentResponse::where('intent_id', $intent->id)->inRandomOrder()->first();

            // if ($intent->save_information) {
            //     ContactInformation::create([
            //         'intent_id' => $intent->id,
            //         'value' => $message
            //     ]);
            // }

            return $response ?? 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
        }

        return 'Lo siento, no entendí tu mensaje, por favor intenta preguntar de otra forma.';
    }

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
