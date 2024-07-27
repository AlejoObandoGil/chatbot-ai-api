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

            $table->uuid('source')->nullable();
            $table->string('sourceHandle')->nullable();
            $table->uuid('target')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('source')->references('id')->on('intents')->onDelete('cascade')->nullable();
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
