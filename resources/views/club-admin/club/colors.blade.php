<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Pengaturan Klub - Warna Jersey</h1>
                <p class="mt-1 text-sm text-blue-100">Atur warna default jersey tim Anda. Warna ini akan dipakai pada DSP dan tampilan lainnya.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="surface-red-blue">
            <form method="POST" action="{{ route('club-admin.club.colors.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <h2 class="panel-title">Warna Jersey Tim</h2>
                <p class="panel-subtitle">Pilih warna baju, celana, dan kaos kaki default tim.</p>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="label">Warna Jersey Tim - Baju</label>
                        <input type="color" name="shirt_color" class="input h-10" value="{{ old('shirt_color', $club?->shirt_color ?? '#FFFFFF') }}">
                        <input type="text" class="input mt-1 text-xs" value="{{ old('shirt_color', $club?->shirt_color ?? '#FFFFFF') }}" readonly>
                    </div>
                    <div>
                        <label class="label">Warna Jersey Tim - Celana</label>
                        <input type="color" name="pants_color" class="input h-10" value="{{ old('pants_color', $club?->pants_color ?? '#000000') }}">
                        <input type="text" class="input mt-1 text-xs" value="{{ old('pants_color', $club?->pants_color ?? '#000000') }}" readonly>
                    </div>
                    <div>
                        <label class="label">Warna Jersey Tim - Kaos Kaki</label>
                        <input type="color" name="socks_color" class="input h-10" value="{{ old('socks_color', $club?->socks_color ?? '#FFFFFF') }}">
                        <input type="text" class="input mt-1 text-xs" value="{{ old('socks_color', $club?->socks_color ?? '#FFFFFF') }}" readonly>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button class="btn-primary" type="submit">Simpan Warna Jersey</button>
                    <a href="{{ route('club-admin.officials.index') }}" class="btn-ghost">Kembali ke Official Tim</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
