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
        Schema::create('intent_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intent_id')->constrained('intents')->onDelete('cascade');
            $table->text('response');
            $table->boolean('is_learning')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intent_responses');
    }
};
