<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Verifikasi Mutasi</h1>
                <p class="mt-1 text-sm text-blue-100">Approve atau reject pengajuan mutasi pemain dari admin klub.</p>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="panel-title">Daftar Mutasi Menunggu Verifikasi</h2>
                <p class="panel-subtitle">Semua pengajuan mutasi yang belum diproses oleh super admin.</p>
            </div>
            <form method="GET" action="{{ route('super-admin.transfers.verification.index') }}" class="w-full sm:w-auto">
                <input name="search" value="{{ request('search') }}" class="search-input w-full sm:w-[320px]" placeholder="Cari pemain / klub...">
            </form>
        </div>

        <div class="table-wrap mt-4">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-slate-500 bg-slate-50">
                        <th class="p-3">Pemain</th>
                        <th class="p-3">Asal</th>
                        <th class="p-3">Tujuan</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3">Status Verifikasi</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transfers as $transfer)
                        <tr class="table-row-hover border-t align-top">
                            <td class="p-3">
                                <p class="font-semibold text-slate-900">{{ $transfer->player->name }}</p>
                                <p class="text-xs text-slate-500">NIK: {{ $transfer->player->nik }}</p>
                            </td>
                            <td class="p-3">{{ $transfer->fromClub?->name }}</td>
                            <td class="p-3">{{ $transfer->toClub?->name }}</td>
                            <td class="p-3">
                                @if ($transfer->document_path)
                                    <details class="group">
                                        <summary class="cursor-pointer text-sm font-medium text-blue-600 hover:text-blue-500">Preview PDF</summary>
                                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <object data="{{ route('super-admin.transfers.preview', $transfer) }}" type="application/pdf" class="h-96 w-full">
                                                <div class="p-4 text-sm text-slate-600">
                                                    Preview PDF tidak didukung di browser ini.
                                                </div>
                                            </object>
                                            <div class="border-t border-slate-200 px-3 py-2 flex items-center justify-end">
                                                <a href="{{ route('super-admin.transfers.preview', $transfer) }}" target="_blank" rel="noopener" class="btn btn-ghost text-sm">
                                                    Lihat
                                                </a>
                                            </div>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-sm text-slate-500">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="status-badge status-warning">{{ ucfirst($transfer->verification_status) }}</span>
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('super-admin.transfers.verification.update', $transfer) }}" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex flex-wrap items-center gap-2">
                                        <select name="status" class="input w-36">
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <input type="text" name="verification_note" class="input min-w-[16rem]" placeholder="Catatan (opsional)">
                                        <button class="btn-primary" type="submit">Simpan</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500">Tidak ada mutasi yang menunggu verifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $transfers->links() }}</div>
    </div>
</x-app-layout>
