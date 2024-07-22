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
        Schema::create('knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('chatbot_id');;
            $table->text('content')->nullable();
            $table->text('links')->nullable();
            $table->boolean('is_learning')->default(false);
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
        Schema::dropIfExists('knowledge');
    }
};
