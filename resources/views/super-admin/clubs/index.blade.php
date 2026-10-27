<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Kelola Klub</h1>
                <p class="mt-1 text-sm text-blue-100">Aktifkan atau nonaktifkan klub peserta kompetisi dengan kontrol cepat.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Klub</p>
                    <p class="super-stat-value">{{ $clubs->total() }}</p>
                </div>
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Klub Aktif</p>
                    <p class="super-stat-value">{{ \App\Models\Club::where('is_active', true)->count() }}</p>
                </div>
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Klub Nonaktif</p>
                    <p class="super-stat-value">{{ \App\Models\Club::where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="section-grid">
        <div class="surface-red-blue lg:col-span-1">
            <h2 class="panel-title">Tambah Klub</h2>
            <p class="panel-subtitle">Buat profil klub baru sebagai peserta kompetisi.</p>
            <form method="POST" action="{{ route('super-admin.clubs.store') }}" class="mt-4 space-y-3">
                @csrf
                <div><label class="label">Nama</label><input type="text" name="name" class="input" required></div>
                <div><label class="label">Kode</label><input type="text" name="code" class="input" required></div>
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
        </div>

        <div class="card lg:col-span-2">
            @if ($errors->any())
                <div class="alert-error mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="toolbar">
                <div>
                    <h2 class="panel-title">Daftar Klub</h2>
                    <p class="panel-subtitle">Kontrol status partisipasi klub secara real-time.</p>
                </div>
                <form method="GET" action="{{ route('super-admin.clubs.index') }}">
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari klub...">
                </form>
            </div>
            <div class="table-wrap mt-4">
                <table>
                    <thead class="text-left text-slate-500"><tr><th class="p-2">Logo</th><th class="p-2">Nama</th><th class="p-2">Kode</th><th class="p-2">Status</th><th class="p-2">Aksi</th></tr></thead>
                    <tbody>
                    @foreach ($clubs as $club)
                        <tr class="table-row-hover border-t">
                            <td class="p-2">
                                @if ($club->logo_path)
                                    <img src="{{ route('super-admin.clubs.logo', $club) }}" alt="Logo {{ $club->name }}" class="h-10 w-10 rounded-full border border-slate-200 object-cover">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs text-slate-500">N/A</div>
                                @endif
                            </td>
                            <td class="p-2">{{ $club->name }}</td>
                            <td class="p-2"><span class="chip-soft">{{ $club->code }}</span></td>
                            <td class="p-2">
                                <span class="status-badge {{ $club->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $club->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-2">
                                <div class="flex flex-wrap gap-2">
                                    <details>
                                        <summary class="btn-secondary cursor-pointer list-none">Ubah</summary>
                                        <form method="POST" action="{{ route('super-admin.clubs.update', $club) }}" class="mt-2 w-72 space-y-2 rounded-xl border border-slate-200 bg-white p-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="label">Nama</label>
                                                <input type="text" name="name" class="input" value="{{ $club->name }}" required>
                                            </div>
                                            <div>
                                                <label class="label">Kode</label>
                                                <input type="text" name="code" class="input" value="{{ $club->code }}" required>
                                            </div>
                                            <div>
                                                <label class="label">Status</label>
                                                <select name="is_active" class="input">
                                                    <option value="1" @selected($club->is_active)>Aktif</option>
                                                    <option value="0" @selected(! $club->is_active)>Nonaktif</option>
                                                </select>
                                            </div>
                                            <button class="btn-primary" type="submit">Perbarui</button>
                                        </form>
                                    </details>

                                    <form method="POST" action="{{ route('super-admin.clubs.toggle', $club) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-secondary" type="submit">Ubah Status</button>
                                    </form>

                                    <form method="POST" action="{{ route('super-admin.clubs.destroy', $club) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $clubs->links() }}</div>
        </div>
    </div>
</x-app-layout>
