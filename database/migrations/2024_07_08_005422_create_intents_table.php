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
            $table->uuid('id')->primary();

            $table->foreignUuid('chatbot_id');
            $table->foreignId('entity_id')->nullable()->constrained('entities');
            $table->string('name');
            $table->boolean('is_choice')->default(false)->index();
            $table->boolean('save_information')->default(false)->index();
            $table->string('information_required')->nullable();
            $table->json('position')->nullable();
            $table->json('data')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chatbot_id')->references('id')->on('chatbots')->onDelete('cascade');

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
