<x-guest-layout>
    <div class="mb-6 border-b border-slate-100 pb-5">
        <span class="chip-soft border-blue-200 bg-blue-50 text-blue-700">Masuk Akun</span>
        <h2 class="mt-3 text-2xl font-bold text-slate-900">Selamat Datang</h2>
        <p class="mt-1 text-sm text-slate-500">Masuk ke akun Anda untuk mengelola kompetisi.</p>
    </div>

    <x-auth-session-status class="alert-success mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <div class="login-field mt-1.5">
                <span class="login-field-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="login-input @error('email') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                    placeholder="nama@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <div class="login-field mt-1.5">
                <span class="login-field-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="login-input @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-slate-300 text-red-600 shadow-sm focus:ring-red-500"
                >
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full py-3 text-base">
            Masuk
        </button>
    </form>
</x-guest-layout>