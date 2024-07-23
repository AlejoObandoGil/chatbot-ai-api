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
        Schema::create('talk_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('intent_id')->nullable()->constrained('intents');
            $table->foreignId('talk_id')->constrained('talks')->onDelete('cascade');
            $table->string('sender');
            $table->text('message');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talk_messages');
    }
};
