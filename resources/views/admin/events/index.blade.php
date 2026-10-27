<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Sistem Kompetisi (Event)</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola format Liga atau Knockout beserta kategori umur.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Total Event</p>
                    <p class="super-stat-value">{{ $events->total() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div class="min-w-0">
                <h2 class="panel-title">Daftar Event</h2>
                <p class="panel-subtitle">Kelola event kompetisi dan pantau statusnya.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('admin.events.index') }}">
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari event...">
                </form>
                <a class="btn-primary whitespace-nowrap" href="{{ route('admin.events.create') }}">Tambah Event</a>
            </div>
        </div>

        <div class="table-wrap mt-4">
            <table>
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Kategori Umur</th>
                        <th class="p-3">Periode</th>
                        <th class="p-3">Tim Peserta</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr class="table-row-hover border-t">
                            <td class="p-3 font-medium text-slate-800">{{ $event->name }}</td>
                            <td class="p-3"><span class="chip-soft">{{ strtoupper($event->competition_type) }}</span></td>
                            <td class="p-3">
                                @if ($event->min_age || $event->max_age)
                                    {{ $event->min_age ?? '?' }} - {{ $event->max_age ?? '?' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3 text-sm text-slate-600">
                                @if ($event->start_date || $event->end_date)
                                    {{ $event->start_date?->format('d M Y') ?? '?' }}
                                    —
                                    {{ $event->end_date?->format('d M Y') ?? '?' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3">
                                <a href="{{ route('admin.events.teams.index', $event) }}" class="chip-soft transition hover:border-red-400 hover:text-red-700">
                                    {{ $event->participants_count }} tim{{ $event->bracket_rounds_count > 0 ? ' · bagan' : '' }}
                                </a>
                            </td>
                            <td class="p-3">
                                <span class="status-badge {{ $event->is_active ? 'status-active' : 'status-inactive' }}">{{ $event->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="action-frame ml-auto justify-end">
                                    <a href="{{ route('admin.events.teams.index', $event) }}" class="btn-primary">Pilih Tim</a>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn-secondary">Ubah</a>
                                    <a href="{{ route('admin.monitoring.index', ['event_id' => $event->id]) }}" class="btn-ghost">Monitoring</a>
                                    <form method="POST" action="{{ route('admin.events.toggle', $event) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-ghost" type="submit">{{ $event->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Hapus event ini? Semua pertandingan terkait juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-sm text-slate-500">Belum ada event. Klik <strong>Tambah Event</strong> untuk membuat event baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
    </div>
</x-app-layout>
