@extends('layouts.guest')

@section('title', 'Masuk')

@php
    $fieldClass = 'flex h-11 items-center gap-3 rounded-xl border bg-[#f7f9fb] px-4 transition focus-within:border-orange focus-within:bg-white focus-within:ring-4 focus-within:ring-orange/20';

    $hasError = $errors->any();

    $showDemo = app()->isLocal();
@endphp

@section('content')

    <main class="grid h-dvh overflow-hidden lg:grid-cols-[minmax(0,1.05fr)_minmax(520px,0.95fr)]">

        {{-- ================================================================
             PANEL MEREK
             Hanya tampil desktop
             ================================================================ --}}

        <section
            class="relative hidden overflow-hidden bg-navy-950 px-12 py-10 text-white lg:flex lg:flex-col [@media(max-height:620px)]:py-6"
        >

            <div class="absolute -left-28 -top-28 size-80 rounded-full border border-white/10"></div>

            <div class="absolute -left-12 -top-12 size-52 rounded-full border border-white/10"></div>

            <div class="absolute -bottom-40 -right-32 size-[520px] rounded-full bg-navy-800/60"></div>

            <div class="absolute bottom-20 right-14 size-52 rounded-full border border-orange/20"></div>


            {{-- Logo --}}

            <div class="relative z-10 flex items-center gap-3">

                <span
                    class="grid size-12 place-items-center rounded-2xl bg-orange text-white shadow-lg shadow-black/20"
                >

                    <x-icon
                        name="cattle"
                        :size="28"
                    />

                </span>

                <div>

                    <strong class="block text-base font-extrabold">
                        Monitoring Ternak
                    </strong>

                    <small class="mt-1 block text-[11px] tracking-wide text-[#9eb0c0]">
                        Sistem Lokasi Real-Time
                    </small>

                </div>

            </div>


            {{-- Deskripsi --}}

            <div class="relative z-10 my-auto max-w-xl">

                <span
                    class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.16em] text-[#ffb77a]"
                >

                    <i class="size-1.5 rounded-full bg-orange"></i>

                    Smart Livestock Monitoring

                </span>


                <h1
                    class="max-w-lg text-4xl font-extrabold leading-tight tracking-tight xl:text-5xl"
                >
                    Pantau ternak Anda dengan lebih aman dan efisien.
                </h1>


                <p
                    class="mt-4 max-w-lg text-sm leading-6 text-[#9eb0c0]"
                >
                    Akses lokasi GPS, status geofence, dan kondisi tracker dalam satu dashboard yang mudah digunakan.
                </p>


                {{-- Fitur --}}

                <div
                    class="mt-8 grid max-w-lg grid-cols-3 gap-3 [@media(max-height:680px)]:hidden"
                >

                    @foreach ([
                        ['location', 'GPS Real-Time', 'Lokasi ternak terkini', 'text-[#ffb77a]'],
                        ['geofence', 'Geofence', 'Batas kandang aman', 'text-[#6ee7b7]'],
                        ['bell', 'Peringatan', 'Notifikasi otomatis', 'text-[#fca5a5]'],
                    ] as [$icon, $title, $text, $tone])

                        <div
                            class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur"
                        >

                            <x-icon
                                :name="$icon"
                                :size="20"
                                class="{{ $tone }}"
                            />

                            <strong class="mt-4 block text-sm">
                                {{ $title }}
                            </strong>

                            <small
                                class="mt-1 block text-[11px] leading-4 text-[#9eb0c0]"
                            >
                                {{ $text }}
                            </small>

                        </div>

                    @endforeach

                </div>

            </div>


            <p class="relative z-10 text-[11px] text-[#7f94a7]">
                Monitoring Ternak v1.0
            </p>

        </section>


        {{-- ================================================================
             FORM LOGIN
             ================================================================ --}}

        <section
            class="flex h-dvh overflow-y-auto bg-white px-5 py-5 sm:px-10 lg:px-16"
        >

            <div class="m-auto w-full max-w-md">


                {{-- ========================================================
                     LOGO MOBILE
                     ======================================================== --}}

                <div class="mb-6 flex items-center gap-3 lg:hidden">

                    <span
                        class="grid size-11 place-items-center rounded-xl bg-orange text-white"
                    >

                        <x-icon
                            name="cattle"
                            :size="24"
                        />

                    </span>

                    <div>

                        <strong class="block text-sm font-extrabold text-navy-900">
                            Monitoring Ternak
                        </strong>

                        <small class="mt-1 block text-[11px] text-muted">
                            Sistem Lokasi Real-Time
                        </small>

                    </div>

                </div>


                {{-- ========================================================
                     HEADER LOGIN
                     ======================================================== --}}

                <p
                    class="text-xs font-extrabold uppercase tracking-[0.16em] text-orange-ink"
                >
                    Selamat datang
                </p>


                <h2
                    class="mt-2 text-2xl font-extrabold tracking-tight text-navy-900 sm:text-3xl"
                >
                    Masuk ke akun Anda
                </h2>


                <p class="mt-2 text-sm leading-6 text-muted">
                    Masukkan email dan password untuk membuka dashboard monitoring.
                </p>


                {{-- ========================================================
                     STATUS
                     ======================================================== --}}

                @if (session('status'))

                    <div
                        role="status"
                        class="mt-4 flex items-start gap-3 rounded-xl border border-inside/30 bg-inside-soft p-3 text-xs text-[#146c43]"
                    >

                        <x-icon
                            name="check-circle"
                            :size="16"
                            class="mt-0.5"
                        />

                        <span>
                            {{ session('status') }}
                        </span>

                    </div>

                @endif


                {{-- ========================================================
                     ERROR LOGIN
                     ======================================================== --}}

                @if ($hasError)

                    <div
                        role="alert"
                        class="mt-4 flex items-start gap-3 rounded-xl border border-outside/30 bg-outside-soft p-3 text-xs text-outside-ink"
                    >

                        <x-icon
                            name="alert"
                            :size="16"
                            class="mt-0.5"
                        />

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                {{-- ========================================================
                     FORM
                     ======================================================== --}}

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    data-login-form
                    class="mt-6 space-y-4"
                >

                    @csrf


                    {{-- ====================================================
                         EMAIL
                         ==================================================== --}}

                    <label class="block">

                        <span
                            class="mb-1.5 block text-xs font-bold text-navy-900"
                        >
                            Alamat Email
                        </span>


                        <span
                            class="{{ $fieldClass }} {{ $errors->has('email') ? 'border-outside' : 'border-line' }}"
                        >

                            <x-icon
                                name="mail"
                                :size="18"
                                class="text-muted"
                            />


                            <input
                                type="email"
                                name="email"
                                data-login-email
                                value="{{ old('email') }}"
                                autocomplete="username"
                                required
                                autofocus
                                placeholder="nama@email.com"

                                @if ($errors->has('email'))
                                    aria-invalid="true"
                                @endif

                                class="min-w-0 flex-1 border-0 bg-transparent text-sm text-navy-900 outline-none placeholder:text-[#8a99a8]"
                            >

                        </span>

                    </label>


                    {{-- ====================================================
                         PASSWORD
                         ==================================================== --}}

                    <div>

                        <div
                            class="mb-1.5 flex items-center justify-between text-xs"
                        >

                            <label
                                for="password"
                                class="font-bold text-navy-900"
                            >
                                Password
                            </label>


                            <button
                                type="button"
                                data-forgot-toggle
                                aria-expanded="false"
                                class="border-0 bg-transparent p-0 font-semibold text-orange-ink hover:underline"
                            >
                                Lupa password?
                            </button>

                        </div>


                        {{-- PASSWORD FIELD --}}

                        <span
                            class="{{ $fieldClass }} {{ $errors->has('password') ? 'border-outside' : 'border-line' }}"
                        >

                            {{-- Icon Lock --}}

                            <x-icon
                                name="lock"
                                :size="18"
                                class="text-muted"
                            />


                            {{-- Input Password --}}

                            <input
                                id="password"
                                type="password"
                                name="password"
                                data-login-password
                                autocomplete="current-password"
                                required
                                placeholder="Masukkan password"
                                class="min-w-0 flex-1 border-0 bg-transparent text-sm text-navy-900 outline-none placeholder:text-[#8a99a8]"
                            >


                            {{-- =================================================
                                 TOGGLE PASSWORD
                                 ================================================= --}}

                            <button
                                type="button"
                                data-password-toggle
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                                title="Tampilkan password"
                                class="grid size-8 shrink-0 place-items-center rounded-lg border-0 bg-transparent p-0 text-[#6f8091] transition hover:bg-[#e9eef2] hover:text-navy-900 focus:outline-none focus:ring-2 focus:ring-orange/30"
                            >

                                {{-- Icon Mata Saat Password Tersembunyi --}}

                                <span data-eye-show>
                                    <x-icon
                                        name="eye"
                                        :size="16"
                                    />
                                </span>


                                {{-- Icon Mata Dicoret Saat Password Terlihat --}}

                                <span data-eye-hide hidden>
                                    <x-icon
                                        name="eye-off"
                                        :size="16"
                                    />
                                </span>

                            </button>

                        </span>


                        {{-- =================================================
                             CAPS LOCK
                             ================================================= --}}

                        <p
                            data-caps-hint
                            hidden
                            class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold text-orange-ink"
                        >

                            <x-icon
                                name="alert"
                                :size="13"
                            />

                            Caps Lock sedang aktif

                        </p>


                        {{-- =================================================
                             FORGOT PASSWORD
                             ================================================= --}}

                        <p
                            data-forgot-note
                            hidden
                            class="mt-2 rounded-lg bg-[#f7f9fb] px-3 py-2 text-[11px] leading-5 text-muted"
                        >
                            Fitur reset password belum tersedia. Hubungi administrator sistem untuk mengatur ulang password Anda.
                        </p>

                    </div>


                    {{-- ====================================================
                         REMEMBER ME
                         ==================================================== --}}

                    <label
                        class="flex cursor-pointer select-none items-center gap-3 py-1 text-sm text-[#3f566a]"
                    >

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="peer sr-only"
                        >


                        <span
                            class="grid size-5 shrink-0 place-items-center rounded-md border-2 border-[#9fb0be] bg-white text-transparent transition peer-checked:border-orange peer-checked:bg-orange peer-checked:text-white peer-focus-visible:ring-4 peer-focus-visible:ring-orange/30"
                        >

                            <x-icon
                                name="check"
                                :size="14"
                                class="stroke-[3]"
                            />

                        </span>

                        Ingat saya di perangkat ini

                    </label>


                    {{-- ====================================================
                         SUBMIT
                         ==================================================== --}}

                    <button
                        type="submit"
                        data-login-submit
                        class="flex h-11 w-full items-center justify-center gap-2 rounded-xl border-0 bg-orange text-sm font-extrabold text-navy-950 shadow-lg shadow-orange/25 transition hover:bg-orange-dark focus-visible:ring-4 focus-visible:ring-orange/30 disabled:cursor-wait disabled:opacity-80"
                    >

                        <span data-submit-label>
                            Masuk ke Dashboard
                        </span>


                        <span data-submit-arrow>

                            <x-icon
                                name="arrow-right"
                                :size="16"
                            />

                        </span>


                        <span
                            data-submit-spinner
                            hidden
                        >

                            <x-icon
                                name="spinner"
                                :size="16"
                                class="animate-spin"
                            />

                        </span>

                    </button>

                </form>


                {{-- ========================================================
                     AKUN DEMONSTRASI
                     ======================================================== --}}

                @if ($showDemo)

                    <div
                        class="mt-4 rounded-xl border border-line bg-[#f7f9fb] p-3"
                    >

                        <div class="flex items-start gap-3">


                            {{-- Icon --}}

                            <span
                                class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#edf3f8] text-navy-700"
                            >

                                <x-icon
                                    name="info"
                                    :size="16"
                                />

                            </span>


                            <div class="min-w-0 flex-1">

                                <strong class="block text-xs text-navy-900">
                                    Akun demonstrasi
                                </strong>


                                <p
                                    class="mt-1 text-[11px] leading-5 text-muted"
                                >

                                    Email:
                                    {{ \Database\Seeders\DatabaseSeeder::DEMO_EMAIL }}

                                    <br>

                                    Password:
                                    {{ \Database\Seeders\DatabaseSeeder::DEMO_PASSWORD }}

                                </p>


                                {{-- =================================================
                                     TOMBOL ISI OTOMATIS
                                     ================================================= --}}

                                <button
                                    type="button"
                                    data-demo-fill
                                    data-email="{{ \Database\Seeders\DatabaseSeeder::DEMO_EMAIL }}"
                                    data-password="{{ \Database\Seeders\DatabaseSeeder::DEMO_PASSWORD }}"
                                    class="mt-2 border-0 bg-transparent p-0 text-[11px] font-bold text-orange-ink hover:underline"
                                >
                                    Isi otomatis
                                </button>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ========================================================
                     FOOTER
                     ======================================================== --}}

                <p
                    class="mt-4 text-center text-[11px] text-[#6f8091]"
                >
                    Dengan masuk, Anda menyetujui kebijakan penggunaan sistem.
                </p>

            </div>

        </section>

    </main>


    {{-- ====================================================================
         JAVASCRIPT
         ==================================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT LOGIN
            |--------------------------------------------------------------------------
            */

            const demoButton = document.querySelector('[data-demo-fill]');
            const emailInput = document.querySelector('[data-login-email]');
            const passwordInput = document.querySelector('[data-login-password]');


            /*
            |--------------------------------------------------------------------------
            | TOGGLE PASSWORD
            |--------------------------------------------------------------------------
            */

            const passwordToggle = document.querySelector('[data-password-toggle]');
            const eyeShow = document.querySelector('[data-eye-show]');
            const eyeHide = document.querySelector('[data-eye-hide]');


            /*
            |--------------------------------------------------------------------------
            | TOMBOL MATA
            |--------------------------------------------------------------------------
            */

            if (passwordToggle && passwordInput) {

                passwordToggle.addEventListener('click', function () {

                    const isVisible = passwordInput.type === 'text';


                    if (isVisible) {

                        /*
                        | Sembunyikan password
                        */

                        passwordInput.type = 'password';

                        passwordToggle.setAttribute(
                            'aria-label',
                            'Tampilkan password'
                        );

                        passwordToggle.setAttribute(
                            'aria-pressed',
                            'false'
                        );

                        passwordToggle.setAttribute(
                            'title',
                            'Tampilkan password'
                        );


                        /*
                        | Tampilkan icon mata
                        */

                        if (eyeShow) {
                            eyeShow.hidden = false;
                        }


                        /*
                        | Sembunyikan icon mata dicoret
                        */

                        if (eyeHide) {
                            eyeHide.hidden = true;
                        }

                    } else {

                        /*
                        | Tampilkan password
                        */

                        passwordInput.type = 'text';

                        passwordToggle.setAttribute(
                            'aria-label',
                            'Sembunyikan password'
                        );

                        passwordToggle.setAttribute(
                            'aria-pressed',
                            'true'
                        );

                        passwordToggle.setAttribute(
                            'title',
                            'Sembunyikan password'
                        );


                        /*
                        | Sembunyikan icon mata
                        */

                        if (eyeShow) {
                            eyeShow.hidden = true;
                        }


                        /*
                        | Tampilkan icon mata dicoret
                        */

                        if (eyeHide) {
                            eyeHide.hidden = false;
                        }

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | CAPS LOCK
            |--------------------------------------------------------------------------
            */

            const capsHint = document.querySelector('[data-caps-hint]');

            if (passwordInput && capsHint) {

                passwordInput.addEventListener('keyup', function (event) {

                    if (event.getModifierState('CapsLock')) {
                        capsHint.hidden = false;
                    } else {
                        capsHint.hidden = true;
                    }

                });


                passwordInput.addEventListener('keydown', function (event) {

                    if (event.getModifierState('CapsLock')) {
                        capsHint.hidden = false;
                    } else {
                        capsHint.hidden = true;
                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | LUPA PASSWORD
            |--------------------------------------------------------------------------
            */

            const forgotToggle = document.querySelector('[data-forgot-toggle]');
            const forgotNote = document.querySelector('[data-forgot-note]');

            if (forgotToggle && forgotNote) {

                forgotToggle.addEventListener('click', function () {

                    const isExpanded =
                        forgotToggle.getAttribute('aria-expanded') === 'true';


                    forgotToggle.setAttribute(
                        'aria-expanded',
                        String(!isExpanded)
                    );


                    forgotNote.hidden = isExpanded;

                });

            }


            /*
            |--------------------------------------------------------------------------
            | ISI OTOMATIS
            |--------------------------------------------------------------------------
            */

            if (demoButton && emailInput && passwordInput) {

                demoButton.addEventListener('click', function () {

                    const email =
                        demoButton.dataset.email || '';

                    const password =
                        demoButton.dataset.password || '';


                    /*
                    | Isi email
                    */

                    emailInput.value = email;


                    /*
                    | Isi password
                    */

                    passwordInput.value = password;


                    /*
                    |--------------------------------------------------------------------------
                    | Pastikan password tetap tersembunyi setelah Isi Otomatis
                    |--------------------------------------------------------------------------
                    */

                    passwordInput.type = 'password';


                    if (passwordToggle) {

                        passwordToggle.setAttribute(
                            'aria-label',
                            'Tampilkan password'
                        );

                        passwordToggle.setAttribute(
                            'aria-pressed',
                            'false'
                        );

                        passwordToggle.setAttribute(
                            'title',
                            'Tampilkan password'
                        );

                    }


                    if (eyeShow) {
                        eyeShow.hidden = false;
                    }


                    if (eyeHide) {
                        eyeHide.hidden = true;
                    }


                    /*
                    | Trigger input event
                    */

                    emailInput.dispatchEvent(
                        new Event('input', {
                            bubbles: true
                        })
                    );


                    emailInput.dispatchEvent(
                        new Event('change', {
                            bubbles: true
                        })
                    );


                    passwordInput.dispatchEvent(
                        new Event('input', {
                            bubbles: true
                        })
                    );


                    passwordInput.dispatchEvent(
                        new Event('change', {
                            bubbles: true
                        })
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Efek tombol
                    |--------------------------------------------------------------------------
                    */

                    const originalText = 'Isi otomatis';

                    demoButton.textContent = 'Data terisi ✓';


                    setTimeout(function () {

                        demoButton.textContent = originalText;

                    }, 1500);

                });

            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT LOGIN
            |--------------------------------------------------------------------------
            */

            const loginForm =
                document.querySelector('[data-login-form]');

            const loginSubmit =
                document.querySelector('[data-login-submit]');

            const submitLabel =
                document.querySelector('[data-submit-label]');

            const submitArrow =
                document.querySelector('[data-submit-arrow]');

            const submitSpinner =
                document.querySelector('[data-submit-spinner]');


            if (loginForm && loginSubmit) {

                loginForm.addEventListener('submit', function () {

                    /*
                    | Cegah tombol diklik berkali-kali
                    */

                    loginSubmit.disabled = true;


                    /*
                    | Ubah tampilan tombol
                    */

                    if (submitLabel) {
                        submitLabel.textContent = 'Memproses...';
                    }


                    if (submitArrow) {
                        submitArrow.hidden = true;
                    }


                    if (submitSpinner) {
                        submitSpinner.hidden = false;
                    }

                });

            }

        });
    </script>

@endsection