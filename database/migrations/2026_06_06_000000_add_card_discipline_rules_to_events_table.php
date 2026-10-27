<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedTinyInteger('yellow_card_threshold')->default(3)->after('end_date');
            $table->unsignedTinyInteger('yellow_card_ban_matches')->default(1)->after('yellow_card_threshold');
            $table->unsignedTinyInteger('red_card_ban_matches')->default(1)->after('yellow_card_ban_matches');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'yellow_card_threshold',
                'yellow_card_ban_matches',
                'red_card_ban_matches',
            ]);
        });
    }
};
