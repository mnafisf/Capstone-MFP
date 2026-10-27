<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Ubah Admin Panitia</h1>
                <p class="mt-1 text-sm text-blue-100">{{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-2xl">
        <h2 class="panel-title">Form Ubah Admin</h2>
        <p class="panel-subtitle">Perbarui data akun admin panitia.</p>

        <form method="POST" action="{{ route('super-admin.admins.update', $user) }}" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="label">Nama</label>
                <input type="text" name="name" class="input" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" class="input" value="{{ old('email', $user->email) }}" required>
            </div>
            <div>
                <label class="label">Password Baru (opsional)</label>
                <input type="password" name="password" class="input" minlength="8">
                <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</p>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a class="btn" href="{{ route('super-admin.admins.index') }}">Batal</a>
                <button class="btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
