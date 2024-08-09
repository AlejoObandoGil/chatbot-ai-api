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
            $table->foreignUuid('chatbot_id');
            $table->text('content')->nullable();
            $table->string('link')->nullable();
            $table->string('document')->nullable();
            $table->string('file_openai_id')->nullable();
            $table->string('vector_store_openai_id')->nullable();
            $table->string('file_vector_openai_id')->nullable();
            $table->text('content_file_openai_id')->nullable();
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
