<x-app-layout>
    @php
        $documentFields = [
            'photo_path' => 'Foto',
            'family_card_path' => 'Kartu Keluarga',
            'club_letter_path' => 'Surat Klub / Surat Keluar',
            'birth_certificate_path' => 'Akta Kelahiran',
            'report_card_path' => 'Ijazah / Raport',
            'privacy_consent_letter_path' => 'Surat Persetujuan Data Pribadi',
        ];
    @endphp

    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Verifikasi Pemain</h1>
                <p class="mt-1 text-sm text-blue-100">Lakukan approve atau reject pemain beserta alasan verifikasi.</p>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="toolbar">
            <div>
                <h2 class="panel-title">Shortlist Verifikasi per Klub</h2>
                <p class="panel-subtitle">Pilih klub untuk melihat pemain yang diajukan dan sedang menunggu verifikasi.</p>
            </div>
            <form method="GET" action="{{ route('admin.verification.index') }}" class="flex flex-wrap items-center gap-3">
                <select name="club_id" class="input min-w-[16rem]" onchange="this.form.submit()">
                    <option value="">— Pilih Klub —</option>
                    @foreach ($clubs as $club)
                        <option value="{{ $club->id }}" @selected($clubId === $club->id)>
                            {{ $club->name }}@if ($club->code) ({{ $club->code }})@endif
                        </option>
                    @endforeach
                </select>
                @if ($clubId)
                    <input name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nama pemain...">
                @endif
            </form>
        </div>

        @if (! $clubId)
            <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Pilih klub terlebih dahulu untuk menampilkan daftar pemain yang sedang didaftarkan.
            </p>
        @else
        <div class="table-wrap mt-4">
        <table>
            <thead><tr class="text-left text-slate-500"><th class="p-2">Nama</th><th class="p-2">Club</th><th class="p-2">NIK</th><th class="p-2">Status</th><th class="p-2">Berkas</th><th class="p-2">Aksi</th></tr></thead>
            <tbody>
                @forelse ($players as $player)
                    <tr class="table-row-hover border-t">
                        <td class="p-2">{{ $player->name }}</td>
                        <td class="p-2">{{ $player->club?->name }}</td>
                        <td class="p-2">{{ $player->nik }}</td>
                        <td class="p-2">
                            <span class="status-badge {{ $player->status === 'approved' ? 'status-active' : ($player->status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ strtoupper($player->status) }}</span>
                        </td>
                        <td class="p-2">
                            <details class="group">
                                <summary class="cursor-pointer text-sm font-medium text-blue-600 hover:text-blue-500">
                                    Lihat berkas
                                </summary>
                                <div class="mt-2 space-y-4 rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
                                    @php
                                        $uploadedDocuments = collect($documentFields)
                                            ->map(function ($label, $field) use ($player) {
                                                return [
                                                    'field' => $field,
                                                    'label' => $label,
                                                    'path' => $player->{$field},
                                                ];
                                            })
                                            ->filter(fn ($document) => filled($document['path']));
                                    @endphp

                                    @forelse ($uploadedDocuments as $document)
                                        @php
                                            $url = route('admin.verification.documents.preview', [
                                                'player' => $player,
                                                'field' => $document['field'],
                                            ]);
                                            $extension = strtolower(pathinfo($document['path'], PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true);
                                            $isPdf = $extension === 'pdf';
                                        @endphp

                                        <div class="rounded-lg border border-slate-200 p-3">
                                            <p class="mb-2 text-sm font-semibold text-slate-700">{{ $document['label'] }}</p>

                                            @if ($isImage)
                                                <img src="{{ $url }}" alt="{{ $document['label'] }} {{ $player->name }}" class="max-h-72 w-full rounded-md object-contain bg-slate-50">
                                            @elseif ($isPdf)
                                                <iframe src="{{ $url }}" class="h-72 w-full rounded-md border border-slate-200"></iframe>
                                            @else
                                                <p class="text-sm text-slate-500">Preview tidak tersedia untuk format ini.</p>
                                            @endif

                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="mt-2 inline-flex text-sm font-medium text-blue-600 hover:text-blue-500">
                                                Buka di tab baru
                                            </a>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Belum ada berkas yang diunggah.</p>
                                    @endforelse
                                </div>
                            </details>
                        </td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('admin.verification.update', $player) }}" class="flex items-center gap-2">
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
                        <td colspan="6" class="p-4 text-center text-slate-500">Tidak ada pemain yang menunggu verifikasi untuk klub ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-4">{{ $players->links() }}</div>
        @endif
    </div>
</x-app-layout>
