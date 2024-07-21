<?php

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\EntityController;
use App\Http\Controllers\Talk\TalkController;
use App\Http\Controllers\Intent\IntentController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Talk\TalkMessageController;
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

            Route::prefix('/{chatbot}')->group(function () {
                Route::prefix('/entity')->group(function () {
                    Route::get('/', [EntityController::class, 'index']);
                    Route::post('/', [EntityController::class, 'store']);
                    Route::put('/', [EntityController::class, 'update']);
                });

                Route::prefix('/intent')->group(function () {
                    Route::get('/', [IntentController::class, 'index']);
                    Route::post('/', [IntentController::class, 'store']);
                    Route::delete('/', [IntentController::class, 'destroy']);
                });

                Route::prefix('/talk')->group(function () {
                    Route::get('/', [TalkController::class, 'index']);
                    Route::post('/', [TalkController::class, 'store']);

                    Route::prefix('/{talk}/message')->group(function () {
                        Route::get('/', [TalkMessageController::class, 'index']);
                        Route::post('/', [TalkMessageController::class, 'store']);
                    });
                });
            });
        });
    });
});
