<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Kelengkapan Klub</h1>
                <p class="mt-1 text-sm text-blue-100">Pantau rekap pemain approved dan official setiap klub peserta.</p>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div>
                <h2 class="panel-title">Rekap Kelengkapan Klub</h2>
                <p class="panel-subtitle">Data pemain dan official untuk setiap klub peserta kompetisi, dengan ringkasan official yang lebih jelas.</p>
            </div>
            <form method="GET" action="{{ route('admin.club-completeness.index') }}">
                <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari klub...">
            </form>
        </div>

        <div class="table-wrap mt-4">
            <table>
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-2">Nama Klub</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Rekap Pemain</th>
                        <th class="p-2">Rasio Disetujui</th>
                        <th class="p-2">Official</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clubs as $club)
                        @php
                            $data = $completenessData->firstWhere('club.id', $club->id);
                        @endphp
                        <tr class="table-row-hover border-t">
                            <td class="p-2">
                                <span class="font-semibold">{{ $club->name }}</span>
                                <span class="ml-2 text-xs text-slate-500">({{ $club->code }})</span>
                            </td>
                            <td class="p-2">
                                <span class="status-badge {{ $club->is_active ? 'status-active' : 'status-inactive' }}">{{ $club->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="p-2">
                                <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-sky-50 px-4 py-3 text-center shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-blue-700">Pemain</p>
                                    <p class="mt-2 text-sm font-semibold text-blue-800">Approved: {{ $data['approved_players'] ?? 0 }}</p>
                                    <p class="text-xs text-amber-700">Pending: {{ $data['pending_players'] ?? 0 }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Total: {{ $data['total_players'] ?? 0 }} pemain</p>
                                </div>
                            </td>
                            <td class="p-2">
                                <div class="rounded-xl border border-violet-100 bg-gradient-to-br from-violet-50 to-fuchsia-50 px-4 py-3 text-center shadow-sm">
                                    <p class="text-lg font-bold text-violet-700">{{ number_format($data['combined_ratio'] ?? 0, 1) }}%</p>
                                    <p class="text-xs text-violet-600">Rasio Aproval</p>
                                </div>
                            </td>
                            <td class="p-2">
                                <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 px-4 py-3 text-center shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Official</p>
                                    <p class="mt-2 text-sm font-semibold text-emerald-800">Approved: {{ $data['approved_officials'] ?? 0 }}</p>
                                    <p class="text-xs text-amber-700">Pending: {{ $data['pending_officials'] ?? 0 }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Total official terdaftar: {{ $data['total_officials'] ?? 0 }}</p>
                                </div>
                            </td>
                            <td class="p-2">
                                <a href="{{ route('admin.club-completeness.show', $club) }}" class="btn-primary">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500">Tidak ada klub ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clubs->links() }}</div>
    </div>
</x-app-layout>
