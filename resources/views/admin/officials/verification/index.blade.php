<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Verifikasi Official</h1>
                <p class="mt-1 text-sm text-blue-100">Lakukan approve atau reject official tim yang diajukan klub.</p>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div>
                <h2 class="panel-title">Shortlist Verifikasi per Klub</h2>
                <p class="panel-subtitle">Pilih klub untuk melihat official yang menunggu verifikasi.</p>
            </div>
            <form method="GET" action="{{ route('admin.officials.verification.index') }}" class="flex flex-wrap items-center gap-3">
                <select name="club_id" class="input min-w-[16rem]" onchange="this.form.submit()">
                    <option value="">— Pilih Klub —</option>
                    @foreach ($clubs as $club)
                        <option value="{{ $club->id }}" @selected($clubId === $club->id)>
                            {{ $club->name }}@if ($club->code) ({{ $club->code }})@endif
                        </option>
                    @endforeach
                </select>
                @if ($clubId)
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nama official...">
                @endif
            </form>
        </div>

        @if (! $clubId)
            <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Pilih klub terlebih dahulu untuk menampilkan daftar official yang sedang diajukan.
            </p>
        @else
        <div class="table-wrap mt-4">
        <table>
            <thead>
                <tr class="text-left text-slate-500">
                    <th class="p-2">Nama</th>
                    <th class="p-2">Klub</th>
                    <th class="p-2">Posisi</th>
                    <th class="p-2">No HP</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($officials as $official)
                    <tr class="table-row-hover border-t">
                        <td class="p-2">{{ $official->name }}</td>
                        <td class="p-2">{{ $official->club?->name }}</td>
                        <td class="p-2">{{ $official->position_label }}</td>
                        <td class="p-2">{{ $official->phone }}</td>
                        <td class="p-2">{{ $official->email }}</td>
                        <td class="p-2">
                            <span class="status-badge status-warning">{{ strtoupper($official->status) }}</span>
                        </td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('admin.officials.verification.update', $official) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="input w-36">
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                <input type="text" name="verification_note" class="input" placeholder="Alasan reject">
                                <button class="btn-primary" type="submit">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-slate-500">Tidak ada official yang menunggu verifikasi untuk klub ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-4">{{ $officials->links() }}</div>
        @endif
    </div>
</x-app-layout>
