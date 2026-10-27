<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = [
            ['name' => 'Garuda Muda FC', 'code' => 'GARUDA', 'is_active' => true],
            ['name' => 'Nusantara United', 'code' => 'NUSA', 'is_active' => true],
            ['name' => 'Satria Biru', 'code' => 'SATRIA', 'is_active' => true],
        ];

        foreach ($clubs as $club) {
            Club::updateOrCreate(['code' => $club['code']], $club);
        }
    }
}
