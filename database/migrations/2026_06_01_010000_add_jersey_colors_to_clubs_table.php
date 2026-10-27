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
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('shirt_color')->nullable()->after('logo_path')->comment('Default team shirt color (hex)');
            $table->string('pants_color')->nullable()->after('shirt_color')->comment('Default team pants color (hex)');
            $table->string('socks_color')->nullable()->after('pants_color')->comment('Default team socks color (hex)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn(['shirt_color', 'pants_color', 'socks_color']);
        });
    }
};
