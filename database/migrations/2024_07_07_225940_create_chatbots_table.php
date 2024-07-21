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
        Schema::create('chatbots', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('user_id')->constrained('users');
            $table->string('name');
            $table->text('description');
            $table->string('type');
            $table->boolean('enabled')->default(false);
            $table->string('script_embed')->nullable();
            $table->string('code')->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbots');
    }
};
