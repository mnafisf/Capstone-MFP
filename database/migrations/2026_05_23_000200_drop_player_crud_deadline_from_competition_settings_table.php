<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_settings', function (Blueprint $table) {
            if (Schema::hasColumn('competition_settings', 'player_crud_deadline')) {
                $table->dropColumn('player_crud_deadline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('competition_settings', function (Blueprint $table) {
            $table->timestamp('player_crud_deadline')->nullable()->after('id');
        });
    }
};
