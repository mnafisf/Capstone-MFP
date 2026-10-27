<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Detail Wasit</h1>
                <p class="mt-1 text-sm text-blue-100">Informasi lengkap wasit.</p>
            </div>
        </div>
    </x-slot>

    <div class="section-grid">
        <div class="card lg:col-span-2">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:items-start">
                <div class="md:col-span-2">
                    <h2 class="panel-title">{{ $referee->name }}</h2>
                    <p class="panel-subtitle">Data lisensi dan dokumen.</p>

                        <div class="mt-4 space-y-2">
                            <p><strong>Jenis Lisensi:</strong> <span class="chip-soft">{{ $referee->license_type ?: '-' }}</span></p>
                            <p><strong>No Lisensi:</strong> <span class="badge">{{ $referee->license_number ?: '-' }}</span></p>
                            <p><strong>Telepon:</strong> <span class="chip-soft">{{ $referee->phone ?: '-' }}</span></p>
                        </div>
                </div>

                <div class="doc-panel">
                    <p class="font-semibold">Dokumen</p>
                    <div class="mt-2 space-y-2">
                        <div>
                            <span class="text-sm text-slate-600">KTP</span>
                            <div class="mt-1">
                                @if ($referee->ktp_path)
                                    <a class="btn" href="{{ asset('storage/' . $referee->ktp_path) }}" target="_blank">Lihat / Unduh</a>
                                @else
                                    <span class="chip-soft">Belum ada</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-sm text-slate-600">Sertifikat Lisensi</span>
                            <div class="mt-1">
                                @if ($referee->license_certificate_path)
                                    <a class="btn" href="{{ asset('storage/' . $referee->license_certificate_path) }}" target="_blank">Lihat / Unduh</a>
                                @else
                                    <span class="chip-soft">Belum ada</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a class="btn" href="{{ route('super-admin.referees.index') }}">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</x-app-layout>
