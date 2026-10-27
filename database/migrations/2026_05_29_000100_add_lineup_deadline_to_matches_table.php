<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->timestamp('lineup_deadline')->nullable()->after('match_date');
        });

        $globalDeadline = DB::table('competition_settings')->value('lineup_deadline');

        if ($globalDeadline) {
            DB::table('matches')
                ->whereNull('lineup_deadline')
                ->update(['lineup_deadline' => $globalDeadline]);
        }
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropColumn('lineup_deadline');
        });
    }
};