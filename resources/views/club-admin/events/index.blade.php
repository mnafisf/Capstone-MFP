<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Pendaftaran Pemain ke Event</h1>
                <p class="mt-1 text-sm text-blue-100">Daftarkan pemain sesuai kategori usia event secara otomatis.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="surface-red-blue">
            <form method="POST" action="{{ route('club-admin.events.register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label" for="event_id">Event</label>
                    <select id="event_id" name="event_id" class="input">@foreach($events as $event)<option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>{{ $event->name }} ({{ $event->min_age }}-{{ $event->max_age }})</option>@endforeach</select>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <label class="label">Pemain</label>
                        @if ($players->isNotEmpty())
                            <button type="button" class="text-xs font-semibold text-blue-700 hover:underline" onclick="document.querySelectorAll('[data-player-checkbox]').forEach((el) => { el.checked = true; })">Pilih semua</button>
                        @endif
                    </div>
                    @error('player_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($players->isEmpty())
                        <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">Belum ada pemain disetujui. Tambahkan dan verifikasi pemain terlebih dahulu.</p>
                    @else
                        <ul class="mt-2 max-h-64 space-y-2 overflow-y-auto rounded-lg border border-slate-200 bg-white p-3">
                            @foreach ($players as $player)
                                <li class="flex items-start gap-2">
                                    <input
                                        id="player_{{ $player->id }}"
                                        type="checkbox"
                                        name="player_ids[]"
                                        value="{{ $player->id }}"
                                        data-player-checkbox
                                        class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500"
                                        @checked(in_array($player->id, array_map('intval', (array) old('player_ids', [])), true))
                                    >
                                    <label for="player_{{ $player->id }}" class="cursor-pointer text-sm text-slate-800">
                                        <span class="font-medium">{{ $player->name }}</span>
                                        <span class="text-slate-500">({{ $player->popular_name ?? '-' }})</span>
                                        <span class="mt-0.5 block text-xs text-slate-500">
                                            TTL: {{ $player->birth_place_date ?? '-' }} | Usia: {{ $player->age ?? '-' }}
                                        </span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button class="btn-primary" type="submit" @disabled($players->isEmpty())>Daftarkan</button>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
            <table>
                <thead><tr class="text-left text-slate-500"><th class="p-2">Event</th><th class="p-2">Nama Lengkap</th><th class="p-2">Nama Punggung</th><th class="p-2">Tempat Tanggal Lahir</th><th class="p-2">Usia</th></tr></thead>
                <tbody>
                    @foreach($registrations as $row)
                        <tr class="table-row-hover border-t">
                            <td class="p-2">{{ $row->event->name }}</td>
                            <td class="p-2">{{ $row->player->name }}</td>
                            <td class="p-2">{{ $row->player->popular_name ?? '-' }}</td>
                            <td class="p-2">{{ $row->player->birth_place_date ?? '-' }}</td>
                            <td class="p-2">{{ $row->player->age ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="mt-4">{{ $registrations->links() }}</div>
        </div>
    </div>
</x-app-layout>
