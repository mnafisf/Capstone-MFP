<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Detail Official</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $official->name }}</h1>
                <p class="mt-1 text-sm text-blue-100">Detail official untuk klub {{ $club->name }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-slate-500">Nama Lengkap</p>
                    <p class="font-bold text-slate-800">{{ $official->name }}</p>

                    <p class="mt-3 text-xs text-slate-500">Posisi</p>
                    <p class="text-slate-700">{{ $official->position_label }}</p>

                    <p class="mt-3 text-xs text-slate-500">Email</p>
                    <p class="text-slate-700">{{ $official->email ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Telepon</p>
                    <p class="text-slate-700">{{ $official->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Status</p>
                    <p class="font-bold">{{ strtoupper($official->status) }}</p>

                    <p class="mt-3 text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-slate-700">{{ $official->updated_at->format('d M Y H:i') }}</p>

                    <p class="mt-3 text-xs text-slate-500">Catatan Verifikasi</p>
                    <p class="text-slate-700">{{ $official->verification_note ?: '-' }}</p>

                    <p class="mt-3 text-xs text-slate-500">Dibuat pada</p>
                    <p class="text-slate-700">{{ $official->created_at->format('d M Y H:i') }}</p>

                    <div class="mt-6 flex items-center gap-2">
                        <a href="{{ route('admin.club-completeness.show', $club) }}" class="btn-secondary">Kembali</a>
                        <form action="{{ route('admin.club-completeness.officials.destroy', ['club' => $club->id, 'official' => $official->id]) }}" method="POST" onsubmit="return confirm('Hapus official ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Hapus Official</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
