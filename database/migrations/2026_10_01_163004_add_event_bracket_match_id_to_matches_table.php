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
        Schema::table('matches', function (Blueprint $table) {
            if (! Schema::hasColumn('matches', 'event_bracket_match_id')) {
                $table->foreignId('event_bracket_match_id')->nullable()->constrained('event_bracket_matches')->nullOnDelete()->after('event_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'event_bracket_match_id')) {
                $table->dropForeignIdFor(\App\Models\EventBracketMatch::class, 'event_bracket_match_id');
                $table->dropColumn('event_bracket_match_id');
            }
        });
    }
};
