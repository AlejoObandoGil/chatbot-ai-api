<?php

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TalkMessageController;
use App\Http\Controllers\Chatbot\ChatbotController;

Route::get('/chatbots/index', [ChatbotController::class, 'index']);
Route::get('/chatbots/openai-api', [OpenAIService::class, 'handleMessage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('/chatbot/{chatbot}')->group(function () {
            Route::get('/index', [ChatbotController::class, 'index']);
            Route::post('/', [ChatbotController::class, 'store']);

            Route::prefix('/talk/{talk}')->group(function () {
                Route::get('/', [TalkMessageController::class, 'show']);
                Route::post('/', [TalkMessageController::class, 'store']);
                Route::put('/', [TalkMessageController::class, 'update']);
                Route::delete('/', [TalkMessageController::class, 'destroy']);
            });
        });
    });
});
