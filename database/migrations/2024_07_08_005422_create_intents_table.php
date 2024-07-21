<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chatbot_id')->constrained('chatbots');
            // $table->foreignId('node_id')->nullable()->constrained('nodes');
            $table->string('name');
            $table->boolean('is_choice')->default(false);
            $table->boolean('save_information')->nullable()->index();
            $table->json('position')->nullable();
            $table->json('data')->nullable();
            $table->string('type');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intents');
    }
};
