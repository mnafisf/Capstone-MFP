<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Master Stadion</h1>
                <p class="mt-1 text-sm text-blue-100">Sumber data stadion aktif untuk jadwal pertandingan.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Stadion</p>
                    <p class="super-stat-value">{{ $stadiums->total() }}</p>
                </div>
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Stadion Aktif</p>
                    <p class="super-stat-value">{{ \App\Models\Stadium::where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div class="min-w-0">
                <h2 class="panel-title">Daftar Stadion</h2>
                <p class="panel-subtitle">Pastikan data stadion valid dan statusnya terkini.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('super-admin.stadiums.index') }}">
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari stadion...">
                </form>
                <a class="btn-primary whitespace-nowrap" href="{{ route('super-admin.stadiums.create') }}">Tambah Stadion</a>
            </div>
        </div>

        <div class="table-wrap mt-4">
            <table>
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kota</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stadiums as $stadium)
                        <tr class="table-row-hover border-t">
                            <td class="p-3">{{ $stadium->name }}</td>
                            <td class="p-3"><span class="chip-soft">{{ $stadium->city ?: '-' }}</span></td>
                            <td class="p-3">
                                <span class="status-badge {{ $stadium->is_active ? 'status-active' : 'status-inactive' }}">{{ $stadium->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('super-admin.stadiums.edit', $stadium) }}" class="btn-secondary">Ubah</a>
                                    <form method="POST" action="{{ route('super-admin.stadiums.toggle', $stadium) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-ghost" type="submit">Ubah Status</button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.stadiums.destroy', $stadium) }}" class="inline" onsubmit="return confirm('Hapus stadion ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-sm text-slate-500">Belum ada stadion. Klik <strong>Tambah Stadion</strong> untuk menambahkan data baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $stadiums->links() }}</div>
    </div>
</x-app-layout>
