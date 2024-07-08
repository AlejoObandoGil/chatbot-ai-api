<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chatbot\ChatbotController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/chatbots', [ChatbotController::class, 'store']);
});
