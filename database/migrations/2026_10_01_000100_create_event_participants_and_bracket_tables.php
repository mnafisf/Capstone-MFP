<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->unsignedSmallInteger('seed')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'club_id']);
            $table->index(['event_id', 'seed']);
        });

        Schema::create('event_bracket_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->unsignedTinyInteger('round_number');
            $table->string('name', 40);
            $table->unsignedSmallInteger('match_count');
            $table->timestamps();

            $table->unique(['event_id', 'round_number']);
        });

        Schema::create('event_bracket_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_bracket_round_id')->constrained('event_bracket_rounds')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->foreignId('home_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('away_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('winner_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('winner_to_match_id')->nullable()->constrained('event_bracket_matches')->nullOnDelete();
            $table->unsignedTinyInteger('winner_to_position')->nullable();
            $table->timestamps();

            $table->unique(['event_bracket_round_id', 'position']);
            $table->index(['home_club_id']);
            $table->index(['away_club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bracket_matches');
        Schema::dropIfExists('event_bracket_rounds');
        Schema::dropIfExists('event_participants');
    }
};
