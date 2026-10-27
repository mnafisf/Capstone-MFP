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
        Schema::table('lineups', function (Blueprint $table) {
            $table->string('shirt_color')->nullable()->comment('Jersey shirt color (hex)');
            $table->string('pants_color')->nullable()->comment('Jersey pants color (hex)');
            $table->string('socks_color')->nullable()->comment('Jersey socks color (hex)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lineups', function (Blueprint $table) {
            $table->dropColumn(['shirt_color', 'pants_color', 'socks_color']);
        });
    }
};
