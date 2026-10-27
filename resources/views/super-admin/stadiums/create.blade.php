<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Tambah Stadion</h1>
                <p class="mt-1 text-sm text-blue-100">Tambahkan stadion yang dapat dipilih di jadwal pertandingan.</p>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-2xl">
        <h2 class="panel-title">Form Tambah Stadion</h2>
        <p class="panel-subtitle">Isi informasi stadion.</p>

        <form method="POST" action="{{ route('super-admin.stadiums.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="label">Nama</label>
                <input name="name" class="input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="label">Kota</label>
                <input name="city" class="input" value="{{ old('city') }}">
            </div>
            <div>
                <label class="label">Alamat</label>
                <input name="address" class="input" value="{{ old('address') }}">
            </div>
            <div>
                <label class="label">Status</label>
                <select name="is_active" class="input">
                    <option value="1" @selected(old('is_active', '1') == '1')>Aktif</option>
                    <option value="0" @selected(old('is_active') === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a class="btn" href="{{ route('super-admin.stadiums.index') }}">Batal</a>
                <button class="btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
