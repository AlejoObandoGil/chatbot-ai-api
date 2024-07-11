<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Services\OpenAIService;

Route::get('/chatbots/index', [ChatbotController::class, 'index']);
Route::get('/chatbots/openai-api', [OpenAIService::class, 'conexionGptApi']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/chatbots/index', [ChatbotController::class, 'index']);
    Route::post('/chatbots', [ChatbotController::class, 'store']);
});
