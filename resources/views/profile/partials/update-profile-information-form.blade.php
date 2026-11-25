<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    @if ($user->hasRole('club-admin') && $user->club)
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-gradient-to-r from-blue-950 via-blue-800 to-indigo-700 px-5 py-6 sm:px-7">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="relative flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/30 bg-white/15 text-2xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->club->code ?: $user->club->name, 0, 3)) }}

                        @if ($user->club->logo_path)
                            <img
                                src="{{ route('profile.club-logo') }}"
                                alt="Logo {{ $user->club->name }}"
                                class="absolute inset-0 h-full w-full bg-white object-contain p-2"
                                onerror="this.remove()"
                            >
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 text-white">
                        <p class="text-sm font-medium text-blue-100">Informasi klub</p>
                        <h3 class="mt-1 break-words text-2xl font-bold tracking-tight">
                            {{ $user->club->name }}
                        </h3>
                        <p class="mt-1 text-sm text-blue-100">
                            Kode klub:
                            <span class="font-semibold text-white">{{ $user->club->code ?: '-' }}</span>
                        </p>
                    </div>

                    <span class="{{ $user->club->is_active
                        ? 'bg-emerald-100 text-emerald-800'
                        : 'bg-slate-200 text-slate-700' }} inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold">
                        <span class="{{ $user->club->is_active ? 'bg-emerald-500' : 'bg-slate-500' }} h-2 w-2 rounded-full"></span>
                        {{ $user->club->is_active ? 'Klub aktif' : 'Klub nonaktif' }}
                    </span>
                </div>
            </div>

            @php
                $clubColors = [
                    ['label' => 'Warna baju', 'value' => $user->club->shirt_color],
                    ['label' => 'Warna celana', 'value' => $user->club->pants_color],
                    ['label' => 'Warna kaus kaki', 'value' => $user->club->socks_color],
                ];
            @endphp

            <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-7 lg:grid-cols-3">
                @foreach ($clubColors as $color)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ $color['label'] }}
                        </p>

                        @if (is_string($color['value']) && preg_match('/^#[0-9a-fA-F]{6}$/', $color['value']))
                            <div class="mt-3 flex items-center gap-3">
                                <span
                                    class="h-10 w-10 rounded-lg border border-slate-300 shadow-sm"
                                    style="background-color: {{ $color['value'] }}"
                                    role="img"
                                    aria-label="{{ $color['label'] }}"
                                ></span>
                                <span class="text-sm font-medium text-slate-700">Warna klub</span>
                            </div>
                        @else
                            <p class="mt-3 text-sm text-slate-500">Belum diatur</p>
                        @endif
                    </div>
                @endforeach
            </div>

        </section>
    @endif

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
