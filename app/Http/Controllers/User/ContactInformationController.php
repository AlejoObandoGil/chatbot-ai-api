<?php

namespace App\Http\Controllers\User;

use App\Models\Talk\Talk;
use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Models\Chatbot\Chatbot;
use App\Http\Controllers\Controller;
use App\Models\User\ContactInformation;

class ContactInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chatbot $chatbot)
    {
        $talks = Talk::whereHas('contactInformation')
            ->where('chatbot_id', $chatbot->id)
            ->with('contactInformation.intent')
            ->get();

        return response()->json([
            'talks' => $talks,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chatbot $chatbot, ContactInformation $contactInformation)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        $contactInformation->update(['status' => $validated['status']]);

        return response()->json([
            'contact_information' => $contactInformation,
            'message' => 'Estado de contacto actualizado!'
        ], 200);
    }
}
