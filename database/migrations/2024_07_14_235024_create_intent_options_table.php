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
        Schema::create('intent_options', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('intent_id');
            $table->string('option');
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
        Schema::dropIfExists('intent_options');
    }
};
