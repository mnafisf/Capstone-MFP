<footer class="home-footer bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 border-t border-blue-700/50">
    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-8 text-blue-300" />
                    <span class="text-2xl font-black tracking-[-0.08em] text-white" style="font-style: italic;">SAPHYR</span>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-blue-200">
                    Platform SAPHYR untuk manajemen event sepak bola. Pantau event, tim, jadwal, dan hasil pertandingan.
                </p>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white">Telusuri</h4>
                <ul class="mt-3 space-y-2 text-sm text-blue-200">
                    <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                    <li><a href="{{ route('home') }}#event" class="transition hover:text-white">Event</a></li>
                    <li><a href="{{ route('home') }}#tim" class="transition hover:text-white">Tim</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white">Akses Login</h4>
                <ul class="mt-3 space-y-2 text-sm text-blue-200">
                    <li><a href="{{ route('login') }}" class="transition hover:text-white">Panitia Event</a></li>
                    <li><a href="{{ route('login') }}" class="transition hover:text-white">Admin Klub</a></li>
                    <li><a href="{{ route('login') }}" class="transition hover:text-white">Super Admin</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white">Informasi</h4>
                <div class="mt-3 space-y-2">
                    <div class="rounded-xl border border-blue-700/50 bg-blue-800/50 px-4 py-3 text-sm text-blue-200">
                        <p class="font-semibold text-white">Kompetisi Aktif</p>
                        <p class="mt-1">{{ $featuredEvent?->name ?? 'Belum ada event' }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-700/50 bg-blue-800/50 px-4 py-3 text-sm text-blue-200">
                        <p class="font-semibold text-white">Total Peserta</p>
                        <p class="mt-1">{{ $totalClubs ?? 0 }} klub · {{ $totalPlayers ?? 0 }} pemain</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-blue-700/50 pt-6 text-xs text-blue-300 sm:flex-row">
            <p>&copy; {{ date('Y') }} SAPHYR. Semua hak dilindungi.</p>
            <div class="flex gap-4">
                <a href="#" class="transition hover:text-white">Syarat Layanan</a>
                <a href="#" class="transition hover:text-white">Privasi</a>
            </div>
        </div>
    </div>
</footer>
