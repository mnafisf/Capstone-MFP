<x-public-layout title="SAPHYR - Daftar Tim" active-section="tim">
    <main class="pb-16">
        <section class="home-section">
            <x-public.section-heading
                eyebrow="Tim"
                title="Daftar Tim"
                subtitle="Semua klub yang berpartisipasi beserta warna seragam dan jumlah pemainnya."
            />

            <form method="GET" action="{{ route('public.teams.index') }}" class="home-search-bar-light mt-6 max-w-2xl">
                <div class="home-search-input-wrap flex-1 border-b border-slate-100 sm:border-b-0 sm:border-r">
                    <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        name="search"
                        placeholder="Cari nama tim atau kode klub..."
                        value="{{ $search }}"
                        class="home-search-input"
                    />
                </div>
                <div class="flex items-center gap-2 px-2">
                    <button type="submit" class="home-btn-primary !px-5 !py-2.5 !text-xs">Cari</button>
                    @if ($search)
                        <a href="{{ route('public.teams.index') }}" class="home-btn-outline-soft">Reset</a>
                    @endif
                </div>
            </form>

            @if ($clubs->count() > 0)
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($clubs as $club)
                        <article class="home-club-card">
                            <x-public.club-badge :club="$club" size="lg" />
                            <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $club->name }}</h3>
                            <p class="mt-1 text-xs text-slate-500">Kode: {{ $club->code ?? '-' }}</p>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ $club->players_count ?? 0 }} pemain · {{ $club->officials_count ?? 0 }} petugas
                            </p>
                            <div class="mt-3 flex w-full flex-wrap items-center justify-center gap-1.5">
                                @foreach (['shirt_color' => 'Baju', 'pants_color' => 'Celana', 'socks_color' => 'Kaus'] as $field => $label)
                                    @if ($club->{$field})
                                        <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                            <span class="h-2.5 w-2.5 rounded-full border border-slate-300" style="background-color: {{ $club->{$field} }};"></span>
                                            {{ $label }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            <a href="{{ route('home', array_filter(['club' => $club->name])) }}#jadwal" class="mt-4 text-xs font-bold uppercase tracking-wide text-red-600 hover:text-red-800">Lihat Jadwal Tim</a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $clubs->links() }}
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                    <p class="text-sm text-slate-500">
                        {{ $search ? 'Tidak ada tim yang cocok dengan pencarian "'.$search.'".' : 'Belum ada tim terdaftar.' }}
                    </p>
                </div>
            @endif
        </section>
    </main>
</x-public-layout>
