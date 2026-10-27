<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Manajemen Pemain Klub</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola roster pemain, dokumen, dan data usia.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="surface-red-blue">
            @php
                $isEditing = isset($editingPlayer) && $editingPlayer;
            @endphp
            <form method="POST" action="{{ $isEditing ? route('club-admin.players.update', $editingPlayer) : route('club-admin.players.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if($isEditing)
                    @method('PUT')
                @endif
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="space-y-3">
                        <div><label class="label">Nama</label><input name="name" class="input" value="{{ old('name', $editingPlayer->name ?? '') }}"></div>
                        <div><label class="label">NIK</label><input name="nik" class="input" value="{{ old('nik', $editingPlayer->nik ?? '') }}"></div>
                        <div><label class="label">Nama Punggung</label><input name="popular_name" class="input" value="{{ old('popular_name', $editingPlayer->popular_name ?? '') }}"></div>
                        <div>
                            <label class="label">Tempat Tanggal Lahir</label>
                            <input
                                name="birth_place_date"
                                class="input"
                                placeholder="Contoh: Yogyakarta 06 Juni 2006"
                                value="{{ old('birth_place_date', $editingPlayer->birth_place_date ?? '') }}"
                            >
                            @error('birth_place_date')
                                <p class="mt-1 text-xs text-red-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div><label class="label">Negara</label><input name="country" class="input" value="{{ old('country', $editingPlayer->country ?? '') }}"></div>
                        <div><label class="label">Kelompok Umur</label>
                            <select name="age_group" class="input">
                                <option value="">Pilih Umur</option>
                                <option value="8" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '8')>Umur 8</option>
                                <option value="9" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '9')>Umur 9</option>
                                <option value="10" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '10')>Umur 10</option>
                                <option value="11" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '11')>Umur 11</option>
                                <option value="12" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '12')>Umur 12</option>
                                <option value="13" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '13')>Umur 13</option>
                                <option value="14" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '14')>Umur 14</option>
                                <option value="15" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '15')>Umur 15</option>
                                <option value="16" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '16')>Umur 16</option>
                                <option value="17" @selected((string) old('age_group', $editingPlayer->age_group ?? '') === '17')>Umur 17</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Nomor Punggung</label>
                            <input type="number" min="1" max="99" name="jersey_number" class="input" value="{{ old('jersey_number', $editingPlayer->jersey_number ?? '') }}">
                            @error('jersey_number')
                                <p class="mt-1 text-xs text-red-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div><label class="label">Jenis Kelamin</label>
                            <select name="gender" class="input">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" @selected(old('gender', $editingPlayer->gender ?? '') === 'laki-laki')>Laki-laki</option>
                                <option value="perempuan" @selected(old('gender', $editingPlayer->gender ?? '') === 'perempuan')>Perempuan</option>
                            </select>
                        </div>
                        <div><label class="label">Kaki Terkuat</label>
                            <select name="dominant_foot" class="input">
                                <option value="">Pilih Kaki Terkuat</option>
                                <option value="kanan" @selected(old('dominant_foot', $editingPlayer->dominant_foot ?? '') === 'kanan')>Kanan</option>
                                <option value="kiri" @selected(old('dominant_foot', $editingPlayer->dominant_foot ?? '') === 'kiri')>Kiri</option>
                            </select>
                        </div>
                        <div><label class="label">Tinggi Badan (cm)</label><input type="number" step="0.01" name="height_cm" class="input" value="{{ old('height_cm', $editingPlayer->height_cm ?? '') }}"></div>
                        <div><label class="label">Berat Badan (kg)</label><input type="number" step="0.01" name="weight_kg" class="input" value="{{ old('weight_kg', $editingPlayer->weight_kg ?? '') }}"></div>
                    </div>

                    <div class="space-y-3">
                        <div><label class="label">Alamat</label><textarea name="address" class="input">{{ old('address', $editingPlayer->address ?? '') }}</textarea></div>
                        <div>
                            <label class="label">Provinsi</label>
                            <select name="province" class="input">
                                <option value="">Pilih Provinsi</option>
                                @foreach($indonesianProvinces as $province)
                                    <option value="{{ $province }}" @selected(old('province', $editingPlayer->province ?? '') === $province)>{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="label">No HP</label><input name="phone" class="input" value="{{ old('phone', $editingPlayer->phone ?? '') }}"></div>
                        <div><label class="label">Email</label><input type="email" name="email" class="input" value="{{ old('email', $editingPlayer->email ?? '') }}"></div>

                        <p class="text-xs text-slate-100/90">Format berkas: PDF, PNG, JPG. Maksimal 1MB per berkas.</p>
                        <div><label class="label">Foto</label><input type="file" accept=".pdf,.png,.jpg" name="photo" class="input"></div>
                        <div><label class="label">Kartu Keluarga</label><input type="file" accept=".pdf,.png,.jpg" name="family_card" class="input"></div>
                        <div><label class="label">Surat Klub / Surat Keluar</label><input type="file" accept=".pdf,.png,.jpg" name="club_letter" class="input"></div>
                        <div><label class="label">Akta Kelahiran</label><input type="file" accept=".pdf,.png,.jpg" name="birth_certificate" class="input"></div>
                        <div><label class="label">Ijazah / Raport</label><input type="file" accept=".pdf,.png,.jpg" name="report_card" class="input"></div>
                        <div><label class="label">Surat Persetujuan Data Pribadi</label><input type="file" accept=".pdf,.png,.jpg" name="privacy_consent_letter" class="input"></div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($isEditing)
                        <button class="btn-primary" type="submit">Perbarui Pemain</button>
                        <a href="{{ route('club-admin.players.index') }}" class="btn-primary">Batal Ubah</a>
                    @else
                        <button class="btn-primary" type="submit" name="submit_action" value="draft">Simpan Pemain</button>
                        <button class="btn-primary" type="submit" name="submit_action" value="final">Tambah Pemain</button>
                    @endif
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
            <table>
                <thead><tr class="text-left text-slate-500"><th class="p-2">Nama</th><th class="p-2">Nama Punggung</th><th class="p-2">Kelompok Umur</th><th class="p-2">No Punggung</th><th class="p-2">Status</th><th class="p-2">Aksi</th></tr></thead>
                <tbody>
                    @foreach($players as $player)
                        <tr class="table-row-hover border-t">
                            <td class="p-2">{{ $player->name }}</td>
                            <td class="p-2">{{ $player->popular_name ?? '-' }}</td>
                            <td class="p-2">{{ $player->age_group ? 'U-'.$player->age_group : '-' }}</td>
                            <td class="p-2">{{ $player->jersey_number ?? '-' }}</td>
                            <td class="p-2">
                                <span class="status-badge {{ $player->status === 'approved' ? 'status-active' : ($player->status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ strtoupper($player->status) }}</span>
                                @if(\Illuminate\Support\Str::startsWith($player->nik, 'DRAFT-'))
                                    <span class="status-badge status-warning">DRAF</span>
                                @endif
                            </td>
                            <td class="p-2">
                                @if($player->status !== 'approved')
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('club-admin.players.index', ['edit_player_id' => $player->id]) }}" class="btn-primary">Ubah</a>
                                        <form method="POST" action="{{ route('club-admin.players.destroy', $player) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-primary" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="mt-4">{{ $players->links() }}</div>
        </div>
    </div>
</x-app-layout>
