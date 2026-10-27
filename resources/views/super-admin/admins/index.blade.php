<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Manajemen Admin Panitia</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola akun admin panitia dengan akses operasional kompetisi.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Admin</p>
                    <p class="super-stat-value">{{ $admins->total() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div class="min-w-0">
                <h2 class="panel-title">Daftar Admin Panitia</h2>
                <p class="panel-subtitle">Monitor akun admin yang aktif di sistem.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('super-admin.admins.index') }}">
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nama admin...">
                </form>
                <a class="btn-primary whitespace-nowrap" href="{{ route('super-admin.admins.create') }}">Tambah Admin</a>
            </div>
        </div>

        <div class="table-wrap mt-4">
            <table>
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="table-row-hover border-t">
                            <td class="p-3">{{ $admin->name }}</td>
                            <td class="p-3">{{ $admin->email }}</td>
                            <td class="p-3 text-right">
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn-secondary">Ubah</a>
                                    <form method="POST" action="{{ route('super-admin.admins.destroy', $admin) }}" onsubmit="return confirm('Hapus admin ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.impersonate', $admin) }}">
                                        @csrf
                                        <button class="btn btn-ghost" type="submit">Masuk sebagai</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-sm text-slate-500">Belum ada admin panitia. Klik <strong>Tambah Admin</strong> untuk membuat akun baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $admins->links() }}</div>
    </div>
</x-app-layout>
