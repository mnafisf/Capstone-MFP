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
            if (! Schema::hasColumn('matches', 'temperature')) {
                $table->integer('temperature')->nullable()->after('match_date')->comment('Temperature in Celsius');
            }
            if (! Schema::hasColumn('matches', 'spectators')) {
                $table->integer('spectators')->nullable()->after('temperature')->comment('Number of spectators');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'temperature')) {
                $table->dropColumn('temperature');
            }
            if (Schema::hasColumn('matches', 'spectators')) {
                $table->dropColumn('spectators');
            }
        });
    }
};
