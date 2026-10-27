<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = Club::query()->orderBy('code')->get();

        if ($clubs->isEmpty()) {
            $this->command?->warn('PlayerSeeder: tidak ada klub. Jalankan ClubSeeder terlebih dahulu.');

            return;
        }

        $rosters = [
            'GARUDA' => [
                ['nik' => '320100000001', 'name' => 'Raka Pratama', 'popular_name' => 'Raka', 'jersey' => 10, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000002', 'name' => 'Dimas Anggara', 'jersey' => 1, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000003', 'name' => 'Fajar Nugroho', 'jersey' => 2, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000004', 'name' => 'Hendra Wijaya', 'jersey' => 3, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000005', 'name' => 'Iqbal Ramadhan', 'jersey' => 4, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000006', 'name' => 'Joko Santoso', 'jersey' => 5, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000007', 'name' => 'Kevin Mahendra', 'jersey' => 6, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000008', 'name' => 'Lutfi Hakim', 'jersey' => 7, 'age_years' => 15, 'status' => 'pending', 'foot' => 'kanan'],
                ['nik' => '320100000009', 'name' => 'Mario Firmansyah', 'jersey' => 8, 'age_years' => 14, 'status' => 'pending', 'foot' => 'kiri'],
                ['nik' => '320100000010', 'name' => 'Naufal Rizki', 'jersey' => 9, 'age_years' => 15, 'status' => 'rejected', 'foot' => 'kanan'],
            ],
            'NUSA' => [
                ['nik' => '320100000011', 'name' => 'Bagas Saputra', 'popular_name' => 'Bagas', 'jersey' => 9, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000012', 'name' => 'Aditya Pratama', 'jersey' => 1, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000013', 'name' => 'Bima Sakti', 'jersey' => 2, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000014', 'name' => 'Candra Diputra', 'jersey' => 3, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000015', 'name' => 'Daffa Maulana', 'jersey' => 4, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000016', 'name' => 'Eko Prasetyo', 'jersey' => 5, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000017', 'name' => 'Farhan Ardiansyah', 'jersey' => 6, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000018', 'name' => 'Galih Permana', 'jersey' => 7, 'age_years' => 15, 'status' => 'pending', 'foot' => 'kanan'],
                ['nik' => '320100000019', 'name' => 'Hafiz Rahman', 'jersey' => 8, 'age_years' => 14, 'status' => 'pending', 'foot' => 'kiri'],
                ['nik' => '320100000020', 'name' => 'Ilham Syahputra', 'jersey' => 10, 'age_years' => 15, 'status' => 'rejected', 'foot' => 'kanan'],
            ],
            'SATRIA' => [
                ['nik' => '320100000021', 'name' => 'Yusuf Ibrahim', 'jersey' => 1, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000022', 'name' => 'Zaki Alfarizi', 'jersey' => 2, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000023', 'name' => 'Ahmad Fauzi', 'jersey' => 3, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000024', 'name' => 'Bryan Lesmana', 'jersey' => 4, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000025', 'name' => 'Cahyo Nugroho', 'jersey' => 5, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kiri'],
                ['nik' => '320100000026', 'name' => 'Doni Setiawan', 'jersey' => 6, 'age_years' => 15, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000027', 'name' => 'Evan Kurniawan', 'jersey' => 7, 'age_years' => 14, 'status' => 'approved', 'foot' => 'kanan'],
                ['nik' => '320100000028', 'name' => 'Fikri Maulana', 'jersey' => 8, 'age_years' => 15, 'status' => 'pending', 'foot' => 'kiri'],
                ['nik' => '320100000029', 'name' => 'Gilang Ramadhan', 'jersey' => 9, 'age_years' => 14, 'status' => 'pending', 'foot' => 'kanan'],
                ['nik' => '320100000030', 'name' => 'Hilman Aditya', 'jersey' => 10, 'age_years' => 15, 'status' => 'rejected', 'foot' => 'kanan'],
            ],
        ];

        $birthCities = ['Bandung', 'Jakarta', 'Surabaya', 'Semarang', 'Yogyakarta'];

        foreach ($clubs as $club) {
            $players = $rosters[$club->code] ?? $this->genericRoster($club->code);

            foreach ($players as $index => $data) {
                $birthCity = $birthCities[$index % count($birthCities)];
                $birthDate = Carbon::now()
                    ->subYears($data['age_years'])
                    ->subMonths($index % 6)
                    ->locale('id');

                Player::updateOrCreate(
                    ['nik' => $data['nik']],
                    [
                        'club_id' => $club->id,
                        'name' => $data['name'],
                        'popular_name' => $data['popular_name'] ?? null,
                        'birth_place_date' => $birthCity.' '.$birthDate->translatedFormat('d F Y'),
                        'jersey_number' => $data['jersey'],
                        'age_group' => 15,
                        'gender' => $data['gender'] ?? 'laki-laki',
                        'country' => 'Indonesia',
                        'dominant_foot' => $data['foot'] ?? 'kanan',
                        'height_cm' => 158 + ($index % 12),
                        'weight_kg' => 46 + ($index % 10),
                        'address' => 'Jl. Pemain Muda No. '.($index + 1).', '.$birthCity,
                        'province' => 'Jawa Barat',
                        'phone' => '0812'.str_pad((string) (1000000 + ($club->id * 100) + $index), 8, '0', STR_PAD_LEFT),
                        'email' => strtolower($club->code).'.pemain'.$data['jersey'].'@football-future.test',
                        'status' => $data['status'] ?? 'approved',
                        'verification_note' => ($data['status'] ?? 'approved') === 'rejected'
                            ? 'Dokumen verifikasi belum lengkap.'
                            : null,
                    ]
                );
            }
        }

        $this->command?->info('PlayerSeeder: '.Player::query()->count().' pemain terdaftar.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function genericRoster(string $clubCode): array
    {
        $players = [];

        for ($jersey = 1; $jersey <= 8; $jersey++) {
            $players[] = [
                'nik' => '320199'.str_pad(substr(md5($clubCode.$jersey), 0, 6), 6, '0', STR_PAD_LEFT),
                'name' => 'Pemain '.$clubCode.' '.$jersey,
                'jersey' => $jersey,
                'age_years' => 14 + ($jersey % 2),
                'status' => $jersey <= 6 ? 'approved' : 'pending',
                'foot' => $jersey % 2 === 0 ? 'kiri' : 'kanan',
            ];
        }

        return $players;
    }
}
