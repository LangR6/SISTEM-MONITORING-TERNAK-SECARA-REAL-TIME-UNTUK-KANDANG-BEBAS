
@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan')
@section('page_subtitle', 'Kelola sistem monitoring ternak')

@php
    /*
    |--------------------------------------------------------------------------
    | TAB AKTIF
    |--------------------------------------------------------------------------
    */
    $tab = $errors->hasAny([
        'current_password',
        'password',
        'password_confirmation'
    ])
        ? 'password'
        : ($errors->any() ? 'profile' : ($tab ?? request('tab', 'profile')));

    /*
    |--------------------------------------------------------------------------
    | CLASS INPUT
    |--------------------------------------------------------------------------
    */
    $inputClass =
        'h-10 w-full rounded-lg border border-[#dce4ea] bg-[#fafcfd] px-[11px] text-[13px] text-navy-900 placeholder:text-[#a0adb7] disabled:bg-[#f1f4f6] disabled:text-[#8a9aa6]';

    /*
    |--------------------------------------------------------------------------
    | CLASS LABEL
    |--------------------------------------------------------------------------
    */
    $labelClass =
        'flex flex-col gap-[7px] [&>span:first-child]:text-[11px] [&>span:first-child]:font-bold [&>span:first-child]:text-navy-900';

    /*
    |--------------------------------------------------------------------------
    | TAB
    |--------------------------------------------------------------------------
    */
    $tabs = [
        'profile' => [
            'Data Diri',
            'user',
            'Ubah Data Diri',
            'Perbarui informasi profil akun Anda.'
        ],
        'password' => [
            'Ubah Password',
            'settings',
            'Ubah Password',
            'Gunakan password yang kuat dan mudah Anda ingat.'
        ],
    ];
@endphp

@section('content')

    <section
        class="grid grid-cols-1 items-start gap-[18px] md:grid-cols-[235px_minmax(0,1fr)]"
        data-settings
        data-active-tab="{{ $tab }}"
    >

        {{-- SIDEBAR PENGATURAN --}}
        <aside class="grid grid-cols-2 overflow-hidden rounded-card border border-line bg-white px-2.5 py-3.5 shadow-card md:block">

            {{-- PROFIL USER --}}
            <div class="col-span-full flex flex-col items-center border-b border-line px-2.5 pb-[22px] pt-[17px]">
                <span class="grid size-14 place-items-center rounded-[15px] bg-navy-800 text-white">
                    <x-icon name="user" :size="25" />
                </span>

                <strong class="mt-2.5 text-xs">
                    {{ $user['name'] }}
                </strong>

                <small class="mt-1 text-[11px] text-muted">
                    {{ $user['role'] }}
                </small>
            </div>

            {{-- MENU TAB --}}
            @foreach ($tabs as $key => $tabData)
                @php
                    $label = $tabData[0];
                    $icon = $tabData[1];
                @endphp

                <button
                    type="button"
                    data-tab="{{ $key }}"
                    aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                    class="group mt-2 grid w-full grid-cols-[20px_1fr_14px] items-center gap-2 rounded-lg px-[9px] py-[11px] text-left text-[11px] font-bold text-[#6b7f90] aria-selected:bg-orange-soft aria-selected:text-navy-900 md:mt-[5px]"
                >
                    <x-icon
                        :name="$icon"
                        :size="17"
                        class="group-aria-selected:text-orange"
                    />

                    <span>{{ $label }}</span>

                    <x-icon
                        name="chevron"
                        :size="14"
                        class="justify-self-end"
                    />
                </button>
            @endforeach

        </aside>

        {{-- CONTENT PENGATURAN --}}
        <div class="min-w-0">

            {{-- PANEL DATA DIRI --}}
            <form
                method="POST"
                action="{{ route('settings.profile') }}"
                data-panel="profile"
                @if ($tab !== 'profile') hidden @endif
                class="overflow-hidden rounded-card border border-line bg-white shadow-card"
            >
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                @include('partials.settings-header', [
                    'icon' => 'user',
                    'title' => $tabs['profile'][2],
                    'text' => $tabs['profile'][3],
                ])

                {{-- FORM DATA DIRI --}}
                <div class="grid grid-cols-1 gap-4 px-4 pb-8 pt-5 min-[441px]:grid-cols-2 md:px-[22px] md:pt-6">

                    {{-- NAMA --}}
                    <label class="{{ $labelClass }}">
                        <span>Nama Lengkap</span>

                        <input
                            name="name"
                            required
                            value="{{ old('name', $user['name']) }}"
                            class="{{ $inputClass }}"
                        >

                        @error('name')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- PERAN --}}
                    <label class="{{ $labelClass }}">
                        <span>Peran</span>

                        <input
                            value="{{ $user['role'] }}"
                            disabled
                            class="{{ $inputClass }}"
                        >
                    </label>

                    {{-- EMAIL --}}
                    <label class="{{ $labelClass }}">
                        <span>Email</span>

                        <input
                            name="email"
                            type="email"
                            required
                            value="{{ old('email', $user['email']) }}"
                            class="{{ $inputClass }}"
                        >

                        @error('email')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- NOMOR TELEPON --}}
                    <label class="{{ $labelClass }}">
                        <span>Nomor Telepon</span>

                        <input
                            name="phone"
                            type="tel"
                            value="{{ old('phone', $user['phone'] ?? '') }}"
                            class="{{ $inputClass }}"
                        >

                        @error('phone')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- ALAMAT --}}
                    <label class="{{ $labelClass }} min-[441px]:col-span-full">
                        <span>Alamat</span>

                        <input
                            name="address"
                            value="{{ old('address', $user['address'] ?? '') }}"
                            class="{{ $inputClass }}"
                        >

                        @error('address')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                </div>

                {{-- ACTION --}}
                @include('partials.settings-actions')
            </form>

            {{-- PANEL UBAH PASSWORD --}}
            <form
                method="POST"
                action="{{ route('settings.password') }}"
                data-panel="password"
                @if ($tab !== 'password') hidden @endif
                class="overflow-hidden rounded-card border border-line bg-white shadow-card"
            >
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                @include('partials.settings-header', [
                    'icon' => 'settings',
                    'title' => $tabs['password'][2],
                    'text' => $tabs['password'][3],
                ])

                {{-- FORM PASSWORD --}}
                <div class="grid max-w-[560px] grid-cols-1 gap-4 px-4 pb-8 pt-5 md:px-[22px] md:pt-6">

                    {{-- PASSWORD SAAT INI --}}
                    <label class="{{ $labelClass }}">
                        <span>Password Saat Ini</span>

                        <div class="relative">
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Masukkan password saat ini"
                                class="{{ $inputClass }} pr-11"
                            >

                            <button
                                type="button"
                                data-toggle-password="current_password"
                                aria-label="Tampilkan password saat ini"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-[#8493a0] transition hover:text-navy-900"
                            >
                                {{-- ICON MATA TERBUKA --}}
                                <svg
                                    data-eye-open
                                    class="h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                {{-- ICON MATA TERTUTUP --}}
                                <svg
                                    data-eye-closed
                                    class="hidden h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                    <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3 3.8"/>
                                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"/>
                                </svg>
                            </button>
                        </div>

                        @error('current_password')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- PASSWORD BARU --}}
                    <label class="{{ $labelClass }}">
                        <span>Password Baru</span>

                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Minimum 8 karakter"
                                class="{{ $inputClass }} pr-11"
                            >

                            <button
                                type="button"
                                data-toggle-password="password"
                                aria-label="Tampilkan password baru"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-[#8493a0] transition hover:text-navy-900"
                            >
                                <svg
                                    data-eye-open
                                    class="h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <svg
                                    data-eye-closed
                                    class="hidden h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                    <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3 3.8"/>
                                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"/>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- KONFIRMASI PASSWORD --}}
                    <label class="{{ $labelClass }}">
                        <span>Konfirmasi Password Baru</span>

                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Ulangi password baru"
                                class="{{ $inputClass }} pr-11"
                            >

                            <button
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Tampilkan konfirmasi password"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-[#8493a0] transition hover:text-navy-900"
                            >
                                <svg
                                    data-eye-open
                                    class="h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <svg
                                    data-eye-closed
                                    class="hidden h-[18px] w-[18px]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                    <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3 3.8"/>
                                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"/>
                                </svg>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <span class="text-[11px] font-medium text-outside">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    {{-- INFO PASSWORD --}}
                    <p class="-mt-[5px] text-[11px] text-muted">
                        Gunakan minimum 8 karakter dengan kombinasi huruf dan angka.
                    </p>

                </div>

                {{-- ACTION --}}
                @include('partials.settings-actions')
            </form>

        </div>
    </section>

    {{-- JAVASCRIPT TAB DAN TOGGLE PASSWORD --}}
    <script>
        (function () {
            function initSettings() {
                const settings = document.querySelector('[data-settings]');

                if (!settings || settings.dataset.initialized === 'true') {
                    return;
                }

                settings.dataset.initialized = 'true';

                const tabButtons = settings.querySelectorAll('[data-tab]');
                const panels = settings.querySelectorAll('[data-panel]');

                /*
                |--------------------------------------------------------------------------
                | GANTI TAB
                |--------------------------------------------------------------------------
                */
                function activateTab(tabName, updateUrl = true) {
                    if (tabName !== 'profile' && tabName !== 'password') {
                        tabName = 'profile';
                    }

                    tabButtons.forEach(function (button) {
                        const isActive = button.dataset.tab === tabName;

                        button.setAttribute(
                            'aria-selected',
                            isActive ? 'true' : 'false'
                        );
                    });

                    panels.forEach(function (panel) {
                        const isActive = panel.dataset.panel === tabName;

                        if (isActive) {
                            panel.removeAttribute('hidden');
                        } else {
                            panel.setAttribute('hidden', '');
                        }
                    });

                    settings.dataset.activeTab = tabName;

                    if (updateUrl) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('tab', tabName);

                        window.history.replaceState({}, '', url);
                    }
                }

                tabButtons.forEach(function (button) {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        activateTab(button.dataset.tab, true);
                    });
                });

                activateTab(settings.dataset.activeTab || 'profile', false);

                /*
                |--------------------------------------------------------------------------
                | TOGGLE LIHAT / SEMBUNYIKAN PASSWORD
                |--------------------------------------------------------------------------
                */
                settings.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const inputId = button.dataset.togglePassword;
                        const input = document.getElementById(inputId);

                        if (!input) {
                            return;
                        }

                        const isCurrentlyHidden = input.type === 'password';

                        input.type = isCurrentlyHidden ? 'text' : 'password';

                        button.setAttribute(
                            'aria-pressed',
                            isCurrentlyHidden ? 'true' : 'false'
                        );

                        const fieldNames = {
                            current_password: 'password saat ini',
                            password: 'password baru',
                            password_confirmation: 'konfirmasi password'
                        };

                        const fieldName = fieldNames[inputId] || 'password';

                        button.setAttribute(
                            'aria-label',
                            isCurrentlyHidden
                                ? 'Sembunyikan ' + fieldName
                                : 'Tampilkan ' + fieldName
                        );

                        const eyeOpen = button.querySelector('[data-eye-open]');
                        const eyeClosed = button.querySelector('[data-eye-closed]');

                        eyeOpen?.classList.toggle('hidden', isCurrentlyHidden);
                        eyeClosed?.classList.toggle('hidden', !isCurrentlyHidden);
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSettings, { once: true });
            } else {
                initSettings();
            }
        })();
    </script>

@endsection
