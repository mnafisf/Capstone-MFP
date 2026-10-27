<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Mutasi Pemain</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola transfer pemain berbasis NIK dengan history permanen.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @php
            $statusLabels = [
                'available' => 'Tersedia',
                'non-available' => 'Tidak Tersedia',
                'blocked' => 'Diblokir',
            ];
            $flowLabels = [
                'requested' => 'Diajukan',
                'accepted' => 'Selesai',
            ];
            $verificationLabels = [
                'pending' => 'Menunggu Verifikasi',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ];
        @endphp
        <div class="surface-red-blue">
            <form method="POST" action="{{ route('club-admin.transfers.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="label">Nama Pemain</label>
                        <select name="player_id" class="input" required>
                            <option value="">Pilih pemain</option>
                            @foreach ($players as $player)
                                <option value="{{ $player->id }}" @selected(old('player_id') == $player->id)>
                                    {{ $player->name }} ({{ $player->nik }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">Klub Tujuan</label><select name="to_club_id" class="input">@foreach($clubs as $club)<option value="{{ $club->id }}">{{ $club->name }}</option>@endforeach</select></div>
                    <div class="lg:col-span-2"><label class="label">Dokumen Mutasi</label><input type="file" name="document" class="input" accept="application/pdf"></div>
                </div>
                <p class="text-xs text-slate-100/90">Format berkas: PDF. Maksimal 1MB.</p>
                <button class="btn-primary" type="submit">Ajukan Mutasi Pemain</button>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
            <table>
                <thead><tr class="text-left text-slate-500"><th class="p-2">Pemain</th><th class="p-2">Asal</th><th class="p-2">Tujuan</th><th class="p-2">Status</th><th class="p-2">Verifikasi</th><th class="p-2">Alur Mutasi</th></tr></thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr class="table-row-hover border-t">
                            <td class="p-2">
                                <p class="font-medium text-slate-900">{{ $transfer->player->name }}</p>
                                <p class="text-xs text-slate-500">{{ $transfer->player->nik }}</p>
                            </td>
                            <td class="p-2">{{ $transfer->fromClub?->name }}</td>
                            <td class="p-2">{{ $transfer->toClub->name }}</td>
                            <td class="p-2"><span class="status-badge {{ $transfer->status === 'available' ? 'status-active' : ($transfer->status === 'blocked' ? 'status-danger' : 'status-warning') }}">{{ $statusLabels[$transfer->status] ?? $transfer->status }}</span></td>
                            <td class="p-2"><span class="status-badge {{ $transfer->verification_status === 'approved' ? 'status-active' : ($transfer->verification_status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ $verificationLabels[$transfer->verification_status] ?? $transfer->verification_status }}</span></td>
                            <td class="p-2">
                                <span class="status-badge status-warning">{{ $flowLabels[$transfer->transfer_state] ?? $transfer->transfer_state }}</span>
                                @if ($transfer->verification_status === 'approved')
                                    <p class="mt-2 text-xs text-emerald-600">Pemain sudah dipindahkan otomatis ke klub tujuan.</p>
                                @elseif ($transfer->verification_status === 'rejected')
                                    <p class="mt-2 text-xs text-red-600">Mutasi ditolak super admin.</p>
                                @else
                                    <p class="mt-2 text-xs text-amber-600">Menunggu approve super admin.</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="mt-4">{{ $transfers->links() }}</div>
        </div>
    </div>
</x-app-layout>
