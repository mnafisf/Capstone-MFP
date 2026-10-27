<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Official Tim</h1>
                <p class="mt-1 text-sm text-blue-100">Ajukan pelatih, manager, dan staff medis. Data akan diverifikasi admin panitia.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="surface-red-blue">
            @php
                $isEditing = isset($editingOfficial) && $editingOfficial;
            @endphp
            <form method="POST" action="{{ $isEditing ? route('club-admin.officials.update', $editingOfficial) : route('club-admin.officials.store') }}" class="space-y-4">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif
                <h2 class="panel-title">{{ $isEditing ? 'Ubah Official' : 'Tambah Official' }}</h2>
                <p class="panel-subtitle">Isi data official tim. Setelah disimpan, status menunggu verifikasi admin.</p>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="label">Nama</label><input name="name" class="input" value="{{ old('name', $editingOfficial->name ?? '') }}" required></div>
                    <div>
                        <label class="label">Posisi</label>
                        <select name="position" class="input" required>
                            <option value="">Pilih Posisi</option>
                            @foreach ($positions as $value => $label)
                                <option value="{{ $value }}" @selected(old('position', $editingOfficial->position ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">No HP</label><input name="phone" class="input" type="tel" value="{{ old('phone', $editingOfficial->phone ?? '') }}" required></div>
                    <div><label class="label">Email</label><input name="email" class="input" type="email" value="{{ old('email', $editingOfficial->email ?? '') }}" required></div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($isEditing)
                        <button class="btn-primary" type="submit">Perbarui Official</button>
                        <a href="{{ route('club-admin.officials.index') }}" class="btn-primary">Batal Ubah</a>
                    @else
                        <button class="btn-primary" type="submit">Ajukan Official</button>
                    @endif
                </div>
            </form>
        </div>

        <div class="card">
            <div class="mb-4 flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="panel-title">Daftar Official</h2>
                    <p class="panel-subtitle">Official yang sudah diajukan beserta status verifikasi.</p>
                </div>
                <a href="{{ route('club-admin.club.colors.edit') }}" class="btn-secondary shrink-0">
                    Pengaturan Warna Jersey
                </a>
            </div>
            <div class="table-wrap">
            <table>
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-2">Nama</th>
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
                            <td class="p-2">{{ $official->position_label }}</td>
                            <td class="p-2">{{ $official->phone }}</td>
                            <td class="p-2">{{ $official->email }}</td>
                            <td class="p-2">
                                <span class="status-badge {{ $official->status === 'approved' ? 'status-active' : ($official->status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ strtoupper($official->status) }}</span>
                                @if ($official->status === 'rejected' && $official->verification_note)
                                    <p class="mt-1 text-xs text-slate-500">{{ $official->verification_note }}</p>
                                @endif
                            </td>
                            <td class="p-2">
                                @if ($official->isPending())
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('club-admin.officials.index', ['edit_official_id' => $official->id]) }}" class="btn-primary">Ubah</a>
                                        <form method="POST" action="{{ route('club-admin.officials.destroy', $official) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-primary" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500">Belum ada official diajukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">{{ $officials->links() }}</div>
        </div>
    </div>
</x-app-layout>

