<?php

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Talk\TalkController;
use App\Http\Controllers\Talk\TalkMessageController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\ContactInformationController;

Route::get('/chatbots/index', [ChatbotController::class, 'index']);
// Route::get('/chatbots/openai-api', [OpenAIService::class, 'handleMessage']);

Route::prefix('/chatbot/{chatbot}')->group(function () {
    Route::prefix('/talk')->group(function () {
        Route::get('/', [TalkController::class, 'show']);
        Route::post('/', [TalkController::class, 'store']);

        Route::prefix('/{talk}/message')->group(function () {
            Route::get('/', [TalkMessageController::class, 'index']);
            Route::post('/', [TalkMessageController::class, 'store']);
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('/chatbot')->group(function () {
            Route::get('/', [ChatbotController::class, 'index']);
            Route::post('/', [ChatbotController::class, 'store']);
            Route::get('/{chatbot}', [ChatbotController::class, 'show']);
            Route::get('/{chatbot}/edit', [ChatbotController::class, 'edit']);
            Route::put('/{chatbot}', [ChatbotController::class, 'update']);
            Route::delete('/{chatbot}', [ChatbotController::class, 'destroy']);

            Route::prefix('/{chatbot}')->group(function () {
                Route::prefix('/talk')->group(function () {
                    Route::get('/', [TalkController::class, 'show']);
                    Route::post('/', [TalkController::class, 'store']);
                    Route::put('/', [TalkController::class, 'update']);
                    Route::delete('/', [TalkController::class, 'destroy']);

                    Route::prefix('/{talk}/message')->group(function () {
                        Route::get('/', [TalkMessageController::class, 'index']);
                        Route::post('/', [TalkMessageController::class, 'store']);
                        Route::put('/', [TalkMessageController::class, 'update']);
                        Route::delete('/', [TalkMessageController::class, 'destroy']);
                    });
                });
            });

            Route::prefix('/{chatbot}')->group(function () {
                Route::prefix('/talk')->group(function () {
                    Route::get('/', [TalkController::class, 'show']);
                    Route::post('/', [TalkController::class, 'store']);
                    Route::put('/', [TalkController::class, 'update']);
                    Route::delete('/', [TalkController::class, 'destroy']);

                    Route::prefix('/{talk}/message')->group(function () {
                        Route::get('/', [TalkMessageController::class, 'index']);
                        Route::post('/', [TalkMessageController::class, 'store']);
                        Route::put('/', [TalkMessageController::class, 'update']);
                        Route::delete('/', [TalkMessageController::class, 'destroy']);
                    });
                });
            });
        });
    });
});
