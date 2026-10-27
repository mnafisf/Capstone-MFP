<x-public-layout title="{{ config('app.name', 'SAPHYR') }} - Masuk" active-section="login">
    <section class="home-auth-wrap">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(239,68,68,0.15),transparent_50%)]"></div>

        <div class="relative mx-auto max-w-md px-4">
            <div class="mb-6 text-center text-white">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Akses Sistem</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight">SAPHYR</h1>
                <p class="mt-1 text-sm text-blue-100">Masuk untuk mengelola kompetisi sepak bola</p>
            </div>

            <div class="home-auth-card">
                {{ $slot }}
            </div>

            <p class="mt-5 text-center">
                <a href="{{ route('home') }}" class="text-sm font-medium text-blue-100 transition hover:text-white hover:underline">
                    &larr; Kembali ke beranda
                </a>
            </p>
        </div>
    </section>
</x-public-layout>
