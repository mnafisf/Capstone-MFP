@php
    $activeFilter = $filter ?? 'semua';
    $filters = [
        'semua' => 'Semua',
        'berlangsung' => 'Berlangsung',
        'mendatang' => 'Mendatang',
        'selesai' => 'Selesai',
        'liga' => 'Liga',
        'knockout' => 'Knockout',
    ];
@endphp

<x-public-layout title="SAPHYR - Daftar Event" active-section="event">
    <main class="pb-16">
        <section class="home-section">
            <x-public.section-heading
                eyebrow="Event"
                title="Daftar Event"
                :subtitle="'Total '.$events->total().' kompetisi. Pilih event untuk melihat jadwal, hasil, dan klasemen.'"
            />

            <div class="home-filter-bar mt-5">
                @foreach ($filters as $key => $label)
                    <a
                        href="{{ $key === 'semua' ? route('public.events.index') : route('public.events.index', ['filter' => $key]) }}"
                        class="{{ $activeFilter === $key ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>

            @if ($events->count() > 0)
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($events as $event)
                        @php
                            $isFinished = $event->end_date?->isPast() ?? false;
                            $isUpcoming = $event->start_date?->isFuture() ?? false;
                            $statusLabel = $isFinished ? 'Selesai' : ($isUpcoming ? 'Akan Datang' : 'Berlangsung');
                            $statusClass = $isFinished ? 'bg-slate-600' : ($isUpcoming ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <article class="home-event-card">
                            <div class="home-event-cover">
                                <div class="absolute left-3 top-3 rounded-full {{ $statusClass }} px-2.5 py-0.5 text-[10px] font-bold uppercase text-white">{{ $statusLabel }}</div>
                                <div class="absolute right-3 top-3 rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold uppercase text-white backdrop-blur">{{ strtoupper($event->competition_type) }}</div>
                                <p class="px-6 text-center text-lg font-bold leading-tight text-white">{{ $event->name }}</p>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    {{ $event->start_date?->format('d M Y') ?? '-' }} – {{ $event->end_date?->format('d M Y') ?? '-' }}
                                </p>
                                <p class="mt-2 text-sm font-semibold text-slate-800">
                                    Kategori usia U-{{ $event->min_age ?? '?' }} s/d U-{{ $event->max_age ?? '?' }}
                                </p>
                                <div class="mt-auto pt-4">
                                    <a href="{{ route('public.events.show', $event->id) }}" class="home-btn-outline-soft w-full">Lihat Detail</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                    <p class="text-sm text-slate-500">
                        {{ $activeFilter === 'semua' ? 'Belum ada event.' : 'Tidak ada event pada filter ini.' }}
                    </p>
                </div>
            @endif
        </section>
    </main>
</x-public-layout>
