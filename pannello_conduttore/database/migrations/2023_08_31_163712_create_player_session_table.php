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
        Schema::create('player_session', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('player_id')->constrained()->onDelete('CASCADE');
            $table->index(['session_id', 'player_id']);
            $table->unique(['session_id', 'player_id']);
            $table->boolean('correct_answer')->default(false);
            $table->text('answer')->nullable();
            $table->unsignedInteger('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_session');
    }
};
