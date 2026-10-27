<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->string('field_position', 20)->nullable()->after('position_type');
        });
    }

    public function down(): void
    {
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->dropColumn('field_position');
        });
    }
};
