<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('birth_place_date')->nullable()->after('popular_name');
        });

        DB::table('players')->orderBy('id')->each(function (object $player) {
            $parts = array_filter([
                $player->birth_city ?? null,
                filled($player->birth_date ?? null)
                    ? Carbon::parse($player->birth_date)->locale('id')->translatedFormat('d F Y')
                    : null,
            ]);

            DB::table('players')->where('id', $player->id)->update([
                'birth_place_date' => $parts !== [] ? implode(' ', $parts) : null,
            ]);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'birth_city']);
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('nik');
            $table->string('birth_city')->nullable()->after('gender');
        });

        DB::table('players')->orderBy('id')->each(function (object $player) {
            $birthDate = null;
            $birthCity = null;

            if (filled($player->birth_place_date ?? null)) {
                $value = trim($player->birth_place_date);
                if (preg_match('/(\d{1,2})\s+(\p{L}+)\s+(\d{4})\s*$/u', $value, $matches)) {
                    $birthCity = trim(substr($value, 0, -strlen($matches[0])));
                    $monthMap = [
                        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                        'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                        'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
                    ];
                    $month = $monthMap[strtolower($matches[2])] ?? null;
                    if ($month) {
                        $birthDate = sprintf('%04d-%02d-%02d', (int) $matches[3], $month, (int) $matches[1]);
                    }
                }
            }

            DB::table('players')->where('id', $player->id)->update([
                'birth_date' => $birthDate,
                'birth_city' => $birthCity ?: null,
            ]);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('birth_place_date');
        });
    }
};
