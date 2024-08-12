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
        Schema::create('edges', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('chatbot_id');
            $table->uuid('source')->nullable();
            $table->uuid('source_handle')->nullable();
            $table->uuid('target')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chatbot_id')->references('id')->on('chatbots')->onDelete('cascade');
            $table->foreign('source')->references('id')->on('intents')->onDelete('cascade')->nullable();
            $table->foreign('source_handle')->references('id')->on('intent_options')->onDelete('cascade')->nullable();
            $table->foreign('target')->references('id')->on('intents')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edges');
    }
};
