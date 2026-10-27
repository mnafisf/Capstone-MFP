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
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->boolean('is_captain')->default(false)->after('field_position');
            $table->integer('shirt_number')->nullable()->after('is_captain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->dropColumn(['is_captain', 'shirt_number']);
        });
    }
};
