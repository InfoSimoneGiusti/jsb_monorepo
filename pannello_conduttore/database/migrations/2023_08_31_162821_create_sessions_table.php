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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('CASCADE');
            $table->text('question');
            $table->unsignedInteger('timestamp'); //unsignedInteger arriva fino a unix epoch attorno al 2100, sufficienti per i nostri scopi ;-)
            $table->unsignedInteger('end_timestamp');
            $table->unsignedInteger('interrupt_timestamp')->nullable();
            $table->boolean('closed')->comment('Indica se la session è conclusa')->default(false);
            $table->boolean('paused')->comment('Indica se la session è in pausa (un giocatore si è prenotato)')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
