<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Intent\Intent;
use App\Http\Controllers\Controller;
use App\Models\Chatbot\Chatbot;
use App\Models\User\ContactInformation;

class ContactInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chatbot $chatbot)
    {
        $intents = Intent::where('chatbot_id', $chatbot->id)->where('save_information', true)->with('contactInformations.talk')->get();

        return response()->json([
            'intents' => $intents,
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactInformation $contactInformation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactInformation $contactInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactInformation $contactInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactInformation $contactInformation)
    {
        //
    }
}
