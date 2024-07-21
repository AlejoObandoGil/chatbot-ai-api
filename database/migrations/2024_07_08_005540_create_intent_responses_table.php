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

            $table->foreignUuid('intent_id');
            $table->text('response');
            $table->boolean('is_learning')->default()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('intent_id')->references('id')->on('intents')->onDelete('cascade');
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
