<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Tambah Wasit</h1>
                <p class="mt-1 text-sm text-blue-100">Masukkan data wasit baru dengan lengkap.</p>
            </div>
        </div>
    </x-slot>

    <div class="section-grid">
        <div class="card lg:col-span-2">
            <h2 class="panel-title">Form Tambah Wasit</h2>
            <p class="panel-subtitle">Isikan data dan upload dokumen pendukung.</p>

            <form method="POST" action="{{ route('super-admin.referees.store') }}" class="space-y-4 mt-4" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="label">Nama</label>
                        <input name="name" class="input w-full" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="label">Jenis Lisensi</label>
                        <select name="license_type" class="input w-full" required>
                            <option value="">Pilih</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">No Lisensi</label>
                        <input name="license_number" class="input w-full" value="{{ old('license_number') }}">
                    </div>
                    <div>
                        <label class="label">Telepon</label>
                        <input name="phone" class="input w-full" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 mt-2">
                    <div>
                        <label class="label">Upload KTP (PDF, max 1 MB)</label>
                        <input type="file" name="ktp" accept="application/pdf" class="input w-full">
                        <p class="text-xs text-slate-500 mt-1">Pastikan file PDF tidak lebih dari 1 MB.</p>
                    </div>
                    <div>
                        <label class="label">Upload Sertifikat Lisensi (PDF, max 1 MB)</label>
                        <input type="file" name="license_certificate" accept="application/pdf" class="input w-full">
                        <p class="text-xs text-slate-500 mt-1">Gunakan sertifikat resmi.</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2 justify-end">
                    <a class="btn" href="{{ route('super-admin.referees.index') }}">Batal</a>
                    <button class="btn-primary" type="submit">Tambah Wasit</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
