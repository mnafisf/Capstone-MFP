<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Tambah Admin Klub</h1>
                <p class="mt-1 text-sm text-blue-100">Buat akun login admin klub sekaligus data klub.</p>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-2xl">
        <h2 class="panel-title">Form Tambah Admin Klub</h2>
        <p class="panel-subtitle">Isi data klub dan akun admin.</p>

        <form method="POST" action="{{ route('super-admin.club-admins.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="label">Nama Klub</label>
                    <input type="text" name="club_name" class="input" value="{{ old('club_name') }}" required>
                </div>
                <div>
                    <label class="label">Kode Klub</label>
                    <input type="text" name="club_code" class="input" value="{{ old('club_code') }}" required>
                </div>
                <div>
                    <label class="label">Logo Klub</label>
                    <input type="file" name="club_logo" class="input" accept="image/*">
                </div>
                <div>
                    <label class="label">Status Klub</label>
                    <select name="club_is_active" class="input">
                        <option value="1" @selected(old('club_is_active', '1') == '1')>Aktif</option>
                        <option value="0" @selected(old('club_is_active') === '0')>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="label">Nama Admin</label>
                    <input type="text" name="name" class="input" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required minlength="8">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a class="btn" href="{{ route('super-admin.club-admins.index') }}">Batal</a>
                <button class="btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
