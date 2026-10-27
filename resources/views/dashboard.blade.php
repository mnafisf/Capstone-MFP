<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Competition Overview</span>
                <h2 class="mt-3 text-3xl font-bold leading-tight">Dashboard Kompetisi</h2>
                <p class="mt-2 text-blue-100">Kelola kompetisi sepak bola secara terstruktur sesuai role Anda.</p>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="card border-l-4 border-l-red-600">
            <p class="text-sm text-slate-500">Total Club</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalClubs }}</p>
        </div>
        <div class="card border-l-4 border-l-blue-600">
            <p class="text-sm text-slate-500">Total Pemain</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalPlayers }}</p>
        </div>
        <div class="card border-l-4 border-l-red-600">
            <p class="text-sm text-slate-500">Total Pertandingan</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalMatches }}</p>
        </div>
        <div class="card border-l-4 border-l-blue-600">
            <p class="text-sm text-slate-500">Total Event</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalEvents }}</p>
        </div>
    </div>

    <div class="mt-6 card">
        <h3 class="text-lg font-semibold text-slate-900">Role Aktif</h3>
        <p class="mt-2 text-slate-600">Anda login sebagai <span class="font-semibold text-blue-700">{{ optional(auth()->user()->role)->name ?? 'Belum ditetapkan' }}</span>.</p>
        <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
            Menu sidebar akan otomatis menyesuaikan akses Super Admin, Admin Panitia, atau Club Admin.
        </div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div class="card">
            <h3 class="text-base font-semibold text-slate-900">Quick Access</h3>
            <p class="mt-2 text-sm text-slate-600">Gunakan sidebar biru di kiri untuk membuka modul sesuai hak akses.</p>
        </div>
        <div class="card bg-gradient-to-br from-red-600 to-red-500 text-white">
            <h3 class="text-base font-semibold">Tema Kompetisi</h3>
            <p class="mt-2 text-sm text-red-100">Primary merah untuk aksi penting, secondary biru untuk navigasi dan struktur panel.</p>
        </div>
    </div>
</x-app-layout>
