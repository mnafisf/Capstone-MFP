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
        Schema::create('stadiums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_number')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('competition_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamp('player_crud_deadline')->nullable();
            $table->timestamp('lineup_deadline')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('competition_type', ['league', 'knockout']);
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('name');
            $table->string('nik')->unique();
            $table->date('birth_date');
            $table->string('document_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('verification_note')->nullable();
            $table->timestamps();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('home_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('away_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('stadium_id')->nullable()->constrained('stadiums')->nullOnDelete();
            $table->foreignId('referee_id')->nullable()->constrained('referees')->nullOnDelete();
            $table->dateTime('match_date');
            $table->text('summary')->nullable();
            $table->text('supervisor_note')->nullable();
            $table->timestamps();
        });

        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->unique()->constrained('matches')->cascadeOnDelete();
            $table->unsignedSmallInteger('home_score')->default(0);
            $table->unsignedSmallInteger('away_score')->default(0);
            $table->timestamps();
        });

        Schema::create('match_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->unsignedSmallInteger('goals')->default(0);
            $table->timestamps();
            $table->unique(['match_id', 'player_id']);
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'player_id']);
        });

        Schema::create('lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['match_id', 'club_id']);
        });

        Schema::create('lineup_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lineup_id')->constrained('lineups')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->enum('position_type', ['starter', 'substitute']);
            $table->timestamps();
            $table->unique(['lineup_id', 'player_id']);
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('from_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('to_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->enum('status', ['available', 'non-available', 'blocked'])->default('available');
            $table->enum('transfer_state', ['requested', 'released', 'accepted'])->default('requested');
            $table->string('document_path')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('lineup_players');
        Schema::dropIfExists('lineups');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('match_player_stats');
        Schema::dropIfExists('match_results');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('players');
        Schema::dropIfExists('events');
        Schema::dropIfExists('competition_settings');
        Schema::dropIfExists('referees');
        Schema::dropIfExists('stadiums');
    }
};
