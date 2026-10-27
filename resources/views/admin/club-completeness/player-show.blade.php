<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Detail Pemain</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $player->name }}</h1>
                <p class="mt-1 text-sm text-blue-100">Detail lengkap pemain untuk klub {{ $club->name }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-slate-500">Nama Lengkap</p>
                    <p class="font-bold text-slate-800">{{ $player->name }}</p>

                    <p class="mt-3 text-xs text-slate-500">Nama Panggilan</p>
                    <p class="text-slate-700">{{ $player->popular_name ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">NIK</p>
                    <p class="text-slate-700">{{ $player->nik ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Tempat / Tanggal Lahir</p>
                    <p class="text-slate-700">{{ $player->birth_place_date ?: '-' }} @if($player->age) · ({{ $player->age }} tahun)@endif</p>

                    <p class="mt-3 text-xs text-slate-500">Jenis Kelamin</p>
                    <p class="text-slate-700">{{ ucfirst($player->gender) ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Kelompok Usia</p>
                    <p class="text-slate-700">{{ $player->age_group ? 'U-' . $player->age_group : '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">No. Telepon</p>
                    <p class="text-slate-700">{{ $player->phone ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Email</p>
                    <p class="text-slate-700">{{ $player->email ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Alamat</p>
                    <p class="text-slate-700">{{ $player->address ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Provinsi</p>
                    <p class="text-slate-700">{{ $player->province ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Tinggi / Berat</p>
                    <p class="text-slate-700">{{ $player->height_cm ? $player->height_cm . ' cm' : '-' }} / {{ $player->weight_kg ? $player->weight_kg . ' kg' : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Status</p>
                    <p class="font-bold">{{ strtoupper($player->status) }}</p>

                    <p class="mt-3 text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-slate-700">{{ $player->updated_at->format('d M Y H:i') }}</p>

                    <div class="mt-4">
                        <p class="text-xs text-slate-500">Foto</p>
                        @if($player->photo_path)
                            <a href="{{ asset('storage/'.$player->photo_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Foto</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-slate-500">Kartu Keluarga</p>
                        @if($player->family_card_path)
                            <a href="{{ asset('storage/'.$player->family_card_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat / Unduh</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-slate-500">Surat Klub</p>
                        @if($player->club_letter_path)
                            <a href="{{ asset('storage/'.$player->club_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat / Unduh</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-slate-500">Akte Kelahiran</p>
                        @if($player->birth_certificate_path)
                            <a href="{{ asset('storage/'.$player->birth_certificate_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat / Unduh</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-slate-500">Raport</p>
                        @if($player->report_card_path)
                            <a href="{{ asset('storage/'.$player->report_card_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat / Unduh</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-slate-500">Surat Persetujuan Privasi</p>
                        @if($player->privacy_consent_letter_path)
                            <a href="{{ asset('storage/'.$player->privacy_consent_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat / Unduh</a>
                        @else
                            <p class="text-slate-500">-</p>
                        @endif
                    </div>

                    <div class="mt-6 flex items-center gap-2">
                        <a href="{{ route('admin.club-completeness.show', $club) }}" class="btn-secondary">Kembali</a>
                        <form action="{{ route('admin.club-completeness.players.destroy', ['club' => $club->id, 'player' => $player->id]) }}" method="POST" onsubmit="return confirm('Hapus pemain ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Hapus Pemain</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

