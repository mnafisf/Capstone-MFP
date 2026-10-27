<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('minute_label', 20);
            $table->foreignId('player_out_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('player_in_id')->constrained('players')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['match_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_substitutions');
    }
};