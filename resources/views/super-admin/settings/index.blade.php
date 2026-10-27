<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Pengaturan Deadline Lineup Pertandingan</h1>
                <p class="mt-1 text-sm text-blue-100">Pilih jadwal pertandingan dulu, lalu buka halaman khusus untuk mengatur deadline lineup.</p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Jadwal Tersedia</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $matches->count() }} pertandingan</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="surface-red-blue max-w-5xl">
        <h2 class="panel-title">Daftar Jadwal Pertandingan</h2>
        <p class="panel-subtitle">Klik tombol atur deadline pada pertandingan yang ingin Anda ubah.</p>
        <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm text-blue-700">
            Setelah masuk ke halaman deadline, Anda bisa menetapkan batas waktu input lineup untuk club admin.
        </div>
        <form method="GET" action="{{ route('super-admin.settings.index') }}" class="mb-4 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="label">Filter Event</label>
                <select name="event_id" class="input">
                    <option value="">Semua event</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" @selected((int) $selectedEventId === (int) $event->id)>{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                @if ($selectedEventId)
                    <a href="{{ route('super-admin.settings.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
        @if ($matches->isEmpty())
            <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Belum ada jadwal pertandingan. Buat jadwal terlebih dahulu sebelum mengatur deadline lineup.</p>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Pertandingan</th>
                            <th class="px-4 py-3">Event</th>
                            <th class="px-4 py-3">Waktu Kickoff</th>
                            <th class="px-4 py-3">Deadline Saat Ini</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($matches as $match)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $match->homeClub->name }} vs {{ $match->awayClub->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $match->event?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $match->match_date->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $match->lineup_deadline?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('super-admin.settings.edit', array_merge(['match' => $match], request()->query())) }}" class="btn-primary inline-flex">Atur Deadline</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
