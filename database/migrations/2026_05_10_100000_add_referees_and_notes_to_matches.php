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
            if (! Schema::hasColumn('matches', 'referee_2_id')) {
                $table->foreignId('referee_2_id')->nullable()->constrained('referees')->nullOnDelete()->after('referee_id');
            }
            if (! Schema::hasColumn('matches', 'referee_3_id')) {
                $table->foreignId('referee_3_id')->nullable()->constrained('referees')->nullOnDelete()->after('referee_2_id');
            }
            if (! Schema::hasColumn('matches', 'match_notes')) {
                $table->text('match_notes')->nullable()->after('supervisor_note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'referee_2_id')) {
                $table->dropForeignIdFor(\App\Models\Referee::class, 'referee_2_id');
                $table->dropColumn('referee_2_id');
            }
            if (Schema::hasColumn('matches', 'referee_3_id')) {
                $table->dropForeignIdFor(\App\Models\Referee::class, 'referee_3_id');
                $table->dropColumn('referee_3_id');
            }
            if (Schema::hasColumn('matches', 'match_notes')) {
                $table->dropColumn('match_notes');
            }
        });
    }
};
