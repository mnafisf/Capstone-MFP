<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $clubAdminRole = Role::where('slug', 'club-admin')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'superadmin@footballfuture.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'club_id' => null,
                'email_verified_at' => now(),
            ]
        );


        User::updateOrCreate(
            ['email' => 'admin@footballfuture.test'],
            [
                'name' => 'Admin Panitia',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'club_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $club = Club::query()->first();

        User::updateOrCreate(
            ['email' => 'clubadmin@footballfuture.test'],
            [
                'name' => 'Club Admin',
                'password' => Hash::make('password'),
                'role_id' => $clubAdminRole->id,
                'club_id' => $club?->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
