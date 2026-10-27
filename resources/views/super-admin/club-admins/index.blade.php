<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Manajemen Admin Klub</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola akun admin klub beserta data klub terkait.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Admin Klub</p>
                    <p class="super-stat-value">{{ $clubAdmins->total() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div class="min-w-0">
                <h2 class="panel-title">Daftar Admin Klub</h2>
                <p class="panel-subtitle">Ubah akun dan kelola data klub terkait.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('super-admin.club-admins.index') }}">
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nama, email, atau klub...">
                </form>
                <a class="btn-primary whitespace-nowrap" href="{{ route('super-admin.club-admins.create') }}">Tambah Admin Klub</a>
            </div>
        </div>

        <div class="table-wrap mt-4">
            <table>
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="p-3">Logo</th>
                        <th class="p-3">Klub</th>
                        <th class="p-3">Nama Admin</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clubAdmins as $clubAdmin)
                        <tr class="table-row-hover border-t">
                            <td class="p-3">
                                @if ($clubAdmin->club?->logo_path)
                                    <img src="{{ route('super-admin.clubs.logo', $clubAdmin->club) }}" alt="Logo {{ $clubAdmin->club?->name }}" class="h-10 w-10 rounded-full border border-slate-200 object-cover">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs text-slate-500">N/A</div>
                                @endif
                            </td>
                            <td class="p-3">
                                <p class="font-semibold text-slate-700">{{ $clubAdmin->club?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $clubAdmin->club?->code ?? '-' }}</p>
                            </td>
                            <td class="p-3">{{ $clubAdmin->name }}</td>
                            <td class="p-3">{{ $clubAdmin->email }}</td>
                            <td class="p-3 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('super-admin.club-admins.edit', $clubAdmin) }}" class="btn-secondary">Ubah</a>
                                    <form method="POST" action="{{ route('super-admin.club-admins.destroy', $clubAdmin) }}" onsubmit="return confirm('Hapus admin klub ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.impersonate', $clubAdmin) }}">
                                        @csrf
                                        <button class="btn btn-ghost" type="submit">Masuk sebagai</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-sm text-slate-500">Belum ada admin klub. Klik <strong>Tambah Admin Klub</strong> untuk membuat akun baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $clubAdmins->links() }}</div>
    </div>
</x-app-layout>
