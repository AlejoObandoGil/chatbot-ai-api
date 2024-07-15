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
        Schema::create('intent_training_phrases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intent_id')->constrained('intents')->onDelete('cascade');
            $table->string('phrase')->index();
            $table->boolean('is_learning')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intent_training_phrases');
    }
};
