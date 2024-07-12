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
            $table->id();

            $table->foreignId('chatbot_id')->constrained('chatbots');
            $table->foreignId('intent_category_id')->nullable()->constrained('intent_categories');
            $table->foreignId('parent_id')->nullable()->constrained('intents');
            $table->string('name');
            $table->string('datatype')->nullable()->index();
            $table->string('group')->nullable()->index();
            $table->integer('level')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
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
