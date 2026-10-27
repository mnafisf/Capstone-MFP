<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Master Wasit</h1>
                <p class="mt-1 text-sm text-blue-100">Data lisensi wasit untuk kebutuhan pertandingan resmi.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Wasit</p>
                    <p class="super-stat-value">{{ $referees->total() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
            <div class="toolbar">
                <div class="min-w-0">
                    <h2 class="panel-title">Daftar Wasit</h2>
                    <p class="panel-subtitle">Pantau daftar wasit yang tersedia untuk event.</p>
                </div>
                <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                    <form method="GET" action="{{ route('super-admin.referees.index') }}">
                        <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari wasit...">
                    </form>
                    <a class="btn-primary whitespace-nowrap" href="{{ route('super-admin.referees.create') }}">Tambah Wasit</a>
                </div>
            </div>
            <div class="table-wrap mt-4">
                <table>
                    <thead>
                        <tr class="text-left text-slate-500 bg-slate-50">
                            <th class="p-3">Nama</th>
                            <th class="p-3">Jenis</th>
                            <th class="p-3">No Lisensi</th>
                            <th class="p-3">Telepon</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($referees as $referee)
                            <tr class="table-row-hover border-t">
                                <td class="p-3">{{ $referee->name }}</td>
                                <td class="p-3"><span class="chip-soft">{{ $referee->license_type ?: '-' }}</span></td>
                                <td class="p-3"><span class="badge">{{ $referee->license_number ?: '-' }}</span></td>
                                <td class="p-3"><span class="chip-soft">{{ $referee->phone ?: '-' }}</span></td>
                                <td class="p-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a title="Detail" href="{{ route('super-admin.referees.show', $referee) }}" class="btn btn-ghost" aria-label="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('super-admin.referees.destroy', $referee) }}" onsubmit="return confirm('Hapus wasit ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Hapus" class="btn btn-danger" type="submit" aria-label="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        <div class="mt-4">{{ $referees->links() }}</div>
    </div>
</x-app-layout>
