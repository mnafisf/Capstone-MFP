<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $club->name }}</h1>
                <p class="mt-1 text-sm text-blue-100">Pantau pemain dan official klub yang sudah diverifikasi.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card border-l-4 border-l-violet-600">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-violet-700">Rasio Disetujui</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Pemain dan Official</h2>
                    <p class="mt-1 text-sm text-slate-500">Satu ringkasan untuk melihat progres kelengkapan klub secara cepat.</p>
                </div>
                <div class="rounded-2xl bg-violet-50 px-4 py-3 text-center min-w-[10rem]">
                    <p class="text-3xl font-black text-violet-700">{{ number_format($combinedRatio, 1) }}%</p>
                    <p class="text-xs text-violet-600">Rasio Aproval</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Pemain</p>
                    <div class="mt-2 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-2xl font-black text-blue-800">{{ number_format($playerStats['approved_ratio'], 1) }}%</p>
                            <p class="text-xs text-slate-500">{{ $playerStats['approved'] }}/{{ $playerStats['total'] }} approved</p>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            <p>Pending: {{ $playerStats['pending'] }}</p>
                            <p>Total: {{ $playerStats['total'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Official</p>
                    <div class="mt-2 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-2xl font-black text-emerald-800">{{ number_format($officialStats['approved_ratio'], 1) }}%</p>
                            <p class="text-xs text-slate-500">{{ $officialStats['approved'] }}/{{ $officialStats['total'] }} approved</p>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            <p>Pending: {{ $officialStats['pending'] }}</p>
                            <p>Total: {{ $officialStats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="card">
                <div class="toolbar">
                    <div>
                        <h2 class="panel-title">Data Official</h2>
                        <p class="panel-subtitle">Pending tampil paling atas dan tabel dibatasi agar tidak memakan ruang.</p>
                    </div>
                    <a href="{{ route('admin.officials.verification.index', ['club_id' => $club->id]) }}" class="btn-secondary">Verifikasi Official</a>
                </div>

                <div class="mt-4 max-h-[28rem] overflow-auto rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Posisi</th>
                                <th class="px-3 py-2 text-left">Kontak</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($officials as $official)
                                <tr class="table-row-hover align-top">
                                    <td class="px-3 py-2">
                                        <p class="font-medium text-slate-900">{{ $official->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $official->email ?: 'Email belum diisi' }}</p>
                                    </td>
                                    <td class="px-3 py-2"><span class="chip-soft text-xs">{{ $official->position_label }}</span></td>
                                    <td class="px-3 py-2">
                                        <p class="text-slate-700">{{ $official->phone ?: '-' }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="status-badge {{ $official->status === 'approved' ? 'status-active' : ($official->status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ strtoupper($official->status) }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.club-completeness.officials.show', ['club' => $club->id, 'official' => $official->id]) }}" class="btn-sm">Lihat</a>
                                            <form action="{{ route('admin.club-completeness.officials.destroy', ['club' => $club->id, 'official' => $official->id]) }}" method="POST" onsubmit="return confirm('Hapus official ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-slate-500">Belum ada official terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500">
                    <p>Official ditampilkan dengan status pending paling atas.</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ $officials->previousPageUrl() }}" class="btn-sm" @if(!$officials->previousPageUrl()) aria-disabled="true" style="opacity:.5;pointer-events:none;" @endif>← Sebelumnya</a>
                        <span class="text-xs text-slate-400">Halaman {{ $officials->currentPage() }} / {{ $officials->lastPage() }}</span>
                        <a href="{{ $officials->nextPageUrl() }}" class="btn-sm" @if(!$officials->nextPageUrl()) aria-disabled="true" style="opacity:.5;pointer-events:none;" @endif>Selanjutnya →</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="toolbar">
                    <div>
                        <h2 class="panel-title">Data Pemain</h2>
                        <p class="panel-subtitle">Semua pemain ditampilkan dalam satu tabel, dengan status pending di atas.</p>
                    </div>
                    <a href="{{ route('admin.verification.index', ['club_id' => $club->id]) }}" class="btn-secondary">Verifikasi Pemain</a>
                </div>

                <div class="mt-4 max-h-72 overflow-auto rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Punggung</th>
                                <th class="px-3 py-2 text-left">Umur</th>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">Gender</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($players as $player)
                                <tr class="table-row-hover align-top">
                                    <td class="px-3 py-2">
                                        <p class="font-medium text-slate-900">{{ $player->name }}</p>
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ $player->popular_name ?: '-' }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $player->age_group ? 'U-' . $player->age_group : '-' }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $player->jersey_number ?: '-' }}</td>
                                    <td class="px-3 py-2"><span class="chip-soft text-xs">{{ ucfirst($player->gender) }}</span></td>
                                    <td class="px-3 py-2">
                                        <span class="status-badge {{ $player->status === 'approved' ? 'status-active' : ($player->status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ strtoupper($player->status) }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.club-completeness.players.show', ['club' => $club->id, 'player' => $player->id]) }}" class="btn-sm">Lihat</a>
                                            <form action="{{ route('admin.club-completeness.players.destroy', ['club' => $club->id, 'player' => $player->id]) }}" method="POST" onsubmit="return confirm('Hapus pemain ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-slate-500">Tidak ada pemain terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500">
                    <p>Pending ditampilkan lebih atas, lalu rejected dan approved.</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ $players->previousPageUrl() }}" class="btn-sm" @if(!$players->previousPageUrl()) aria-disabled="true" style="opacity:.5;pointer-events:none;" @endif>← Sebelumnya</a>
                        <span class="text-xs text-slate-400">Halaman {{ $players->currentPage() }} / {{ $players->lastPage() }}</span>
                        <a href="{{ $players->nextPageUrl() }}" class="btn-sm" @if(!$players->nextPageUrl()) aria-disabled="true" style="opacity:.5;pointer-events:none;" @endif>Selanjutnya →</a>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
