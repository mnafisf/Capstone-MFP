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
        Schema::table('players', function (Blueprint $table) {
            $table->string('popular_name')->nullable()->after('name');
            $table->unsignedTinyInteger('jersey_number')->nullable()->after('birth_date');
            $table->unsignedTinyInteger('age_group')->nullable()->after('jersey_number');
            $table->enum('gender', ['laki-laki', 'perempuan'])->nullable()->after('age_group');
            $table->string('birth_city')->nullable()->after('gender');
            $table->string('country')->nullable()->after('birth_city');
            $table->enum('dominant_foot', ['kanan', 'kiri'])->nullable()->after('country');
            $table->decimal('height_cm', 5, 2)->nullable()->after('dominant_foot');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('height_cm');
            $table->string('address', 500)->nullable()->after('weight_kg');
            $table->string('province')->nullable()->after('address');
            $table->string('city')->nullable()->after('province');
            $table->string('phone')->nullable()->after('city');
            $table->string('email')->nullable()->unique()->after('phone');

            $table->string('photo_path')->nullable()->after('email');
            $table->string('family_card_path')->nullable()->after('photo_path');
            $table->string('club_letter_path')->nullable()->after('family_card_path');
            $table->string('birth_certificate_path')->nullable()->after('club_letter_path');
            $table->string('report_card_path')->nullable()->after('birth_certificate_path');
            $table->string('privacy_consent_letter_path')->nullable()->after('report_card_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_email_unique');

            $table->dropColumn([
                'popular_name',
                'jersey_number',
                'age_group',
                'gender',
                'birth_city',
                'country',
                'dominant_foot',
                'height_cm',
                'weight_kg',
                'address',
                'province',
                'city',
                'phone',
                'email',
                'photo_path',
                'family_card_path',
                'club_letter_path',
                'birth_certificate_path',
                'report_card_path',
                'privacy_consent_letter_path',
            ]);
        });
    }
};
