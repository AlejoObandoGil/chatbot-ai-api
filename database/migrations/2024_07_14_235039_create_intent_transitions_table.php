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
        Schema::create('intent_transitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_intent_id')->constrained('intents')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('intent_options')->onDelete('cascade');
            $table->foreignId('child_intent_id')->constrained('intents')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intent_transitions');
    }
};
