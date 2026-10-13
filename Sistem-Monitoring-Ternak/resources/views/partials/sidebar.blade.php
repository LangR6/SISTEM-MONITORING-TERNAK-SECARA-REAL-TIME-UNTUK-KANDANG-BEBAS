@php
    $menus = [
        [
            'route' => 'dashboard',
            'match' => 'dashboard',
            'label' => 'Dashboard',
            'icon' => 'dashboard',
        ],
        [
            'route' => 'locations',
            'match' => 'locations',
            'label' => 'Lokasi Ternak',
            'icon' => 'location',
        ],
        [
            'route' => 'geofence.edit',
            'match' => 'geofence.*',
            'label' => 'Pengaturan Geofence',
            'icon' => 'geofence',
        ],
        [
            'route' => 'settings',
            'match' => 'settings*',
            'label' => 'Pengaturan',
            'icon' => 'settings',
        ],
    ];
@endphp


{{-- ========================================================= --}}
{{-- SIDEBAR --}}
{{-- ========================================================= --}}

<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-30 flex w-[260px] -translate-x-full flex-col bg-navy-950 text-[#c8d3dc] shadow-[8px_0_30px_rgba(3,17,31,0.08)] transition-transform duration-200 data-[open=true]:translate-x-0 md:w-[220px] md:translate-x-0 lg:w-[250px]"
>

    {{-- ===================================================== --}}
    {{-- HEADER SIDEBAR --}}
    {{-- ===================================================== --}}

    <div
        class="flex h-[72px] items-center gap-3 border-b border-white/10 px-[22px] md:h-[82px]"
    >

        {{-- LOGO --}}
        <span
            class="grid size-[42px] shrink-0 place-items-center rounded-xl bg-orange text-white"
        >
            <x-icon
                name="cattle"
                :size="25"
            />
        </span>


        {{-- BRAND --}}
        <div class="flex min-w-0 flex-col">

            <strong
                class="whitespace-nowrap text-sm leading-tight text-white"
            >
                Monitoring Ternak
            </strong>

            <small
                class="mt-1 text-[10px] tracking-[0.02em] text-[#7f94a7]"
            >
                Sistem Lokasi Real-Time
            </small>

        </div>


        {{-- CLOSE SIDEBAR MOBILE --}}
        <button
            type="button"
            data-sidebar-close
            aria-label="Tutup menu"
            class="ml-auto border-0 bg-transparent p-1 text-white md:hidden"
        >
            <x-icon name="x" />
        </button>

    </div>


    {{-- ===================================================== --}}
    {{-- NAVIGASI --}}
    {{-- ===================================================== --}}

    <nav
        aria-label="Navigasi utama"
        class="px-3 pb-4 pt-[27px]"
    >

        {{-- LABEL --}}
        <p
            class="mx-3 mb-3 text-[10px] font-extrabold tracking-[0.14em] text-[#587188]"
        >
            MENU UTAMA
        </p>


        {{-- MENU --}}
        @foreach ($menus as $menu)

            @php
                $active = request()->routeIs($menu['match']);
            @endphp


            <a
                href="{{ route($menu['route']) }}"
                @if ($active)
                    aria-current="page"
                @endif
                class="relative flex h-12 w-full items-center gap-[13px] rounded-[9px] px-3.5 text-[13px] font-semibold transition-colors hover:bg-white/5 hover:text-white {{ $active ? 'bg-navy-800 text-white shadow-[inset_3px_0_0_var(--color-orange)]' : 'text-[#9eb0c0]' }}"
            >

                {{-- ICON --}}
                <x-icon
                    :name="$menu['icon']"
                    :size="19"
                    :class="$active ? 'text-orange' : ''"
                />


                {{-- LABEL --}}
                <span>
                    {{ $menu['label'] }}
                </span>


                {{-- ACTIVE DOT --}}
                @if ($active)

                    <i
                        class="absolute right-[15px] size-[5px] rounded-full bg-orange"
                    ></i>

                @endif

            </a>

        @endforeach

    </nav>


    {{-- ===================================================== --}}
    {{-- STATUS LORA GATEWAY --}}
    {{-- ===================================================== --}}

    <div
        class="mx-4 mb-3.5 mt-auto flex items-center gap-[11px] rounded-[10px] border border-white/[0.07] bg-white/[0.055] p-[13px]"
    >

        {{-- ICON GATEWAY --}}
        <span
            class="grid size-[34px] place-items-center rounded-lg bg-inside/[0.13] text-[#5bd098]"
        >

            <x-icon
                name="gateway"
                :size="19"
            />

        </span>


        {{-- INFORMASI GATEWAY --}}
        <div class="flex flex-col">

            <strong class="text-xs text-[#dce5eb]">
                LoRa Gateway
            </strong>

            <small
                class="mt-1 flex items-center gap-[5px] text-[11px] text-[#7f94a7]"
            >

                <i
                    class="size-1.5 rounded-full bg-inside shadow-[0_0_0_3px_rgba(32,166,106,0.12)]"
                ></i>

                Online

            </small>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- LOGOUT --}}
    {{-- ===================================================== --}}

    <form
        id="logout-form"
        method="POST"
        action="{{ route('logout') }}"
        class="mx-[17px]"
    >

        @csrf

        <button
            type="button"
            onclick="openLogoutModal()"
            class="flex w-full items-center gap-3 border-0 border-t border-white/[0.08] bg-transparent px-[11px] py-3.5 text-[13px] font-semibold text-[#8498aa] transition-colors hover:text-white"
        >

            <x-icon
                name="logout"
                :size="19"
            />

            <span>
                Logout
            </span>

        </button>

    </form>


    {{-- ===================================================== --}}
    {{-- FOOTER --}}
    {{-- ===================================================== --}}

    <div
        class="px-[26px] pb-[19px] pt-3 text-[10px] text-[#4f677d]"
    >

        Monitoring Ternak

        <span class="float-right">
            v1.0
        </span>

    </div>

</aside>


{{-- ========================================================= --}}
{{-- MODAL KONFIRMASI LOGOUT --}}
{{-- ========================================================= --}}

<div
    id="logout-modal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-[#06182b]/55 px-4 backdrop-blur-[6px]"
>

    {{-- ===================================================== --}}
    {{-- KOTAK MODAL --}}
    {{-- ===================================================== --}}

    <div
        id="logout-modal-content"
        role="dialog"
        aria-modal="true"
        aria-labelledby="logout-modal-title"
        class="relative w-full max-w-[380px] scale-95 rounded-2xl bg-white px-7 py-6 opacity-0 shadow-[0_20px_60px_rgba(0,0,0,0.25)] transition-all duration-200"
    >

        {{-- ================================================= --}}
        {{-- CLOSE BUTTON --}}
        {{-- ================================================= --}}

        <button
            type="button"
            onclick="closeLogoutModal()"
            aria-label="Tutup dialog"
            class="absolute right-4 top-4 grid size-8 place-items-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M18 6L6 18"></path>
                <path d="M6 6L18 18"></path>
            </svg>

        </button>


        {{-- ================================================= --}}
        {{-- ICON LOGOUT --}}
        {{-- ================================================= --}}

        <div class="mb-4 flex justify-center">

            <div
                class="grid size-[54px] place-items-center rounded-full bg-orange/10 text-orange"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="25"
                    height="25"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                    ></path>

                    <polyline
                        points="16 17 21 12 16 7"
                    ></polyline>

                    <line
                        x1="21"
                        y1="12"
                        x2="9"
                        y2="12"
                    ></line>

                </svg>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- JUDUL --}}
        {{-- ================================================= --}}

        <h2
            id="logout-modal-title"
            class="text-center text-[18px] font-bold text-navy-950"
        >
            Keluar dari Sistem?
        </h2>


        {{-- ================================================= --}}
        {{-- DESKRIPSI --}}
        {{-- ================================================= --}}

        <p
            class="mx-auto mt-2 max-w-[290px] text-center text-[13px] leading-5 text-gray-500"
        >
            Apakah Anda yakin ingin keluar dari akun

            <span class="font-semibold text-gray-700">
                Monitoring Ternak
            </span>

            ?
        </p>


        {{-- ================================================= --}}
        {{-- BUTTON --}}
        {{-- ================================================= --}}

        <div class="mt-6 flex gap-3">

            {{-- BATAL --}}
            <button
                type="button"
                onclick="closeLogoutModal()"
                class="h-11 flex-1 rounded-[10px] border border-gray-200 bg-white px-4 text-[13px] font-semibold text-gray-600 transition hover:bg-gray-50"
            >
                Batal
            </button>


            {{-- YA LOGOUT --}}
            <button
                type="button"
                onclick="confirmLogout()"
                class="h-11 flex-1 rounded-[10px] bg-orange px-4 text-[13px] font-semibold text-white shadow-sm transition hover:opacity-90"
            >
                Ya, Logout
            </button>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT MODAL LOGOUT --}}
{{-- ========================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL LOGOUT
    |--------------------------------------------------------------------------
    */

    function openLogoutModal() {

        const modal = document.getElementById('logout-modal');

        const content = document.getElementById(
            'logout-modal-content'
        );


        if (!modal || !content) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Tampilkan modal
        |--------------------------------------------------------------------------
        */

        modal.classList.remove('hidden');

        modal.classList.add('flex');


        /*
        |--------------------------------------------------------------------------
        | Cegah halaman di belakang ikut discroll
        |--------------------------------------------------------------------------
        */

        document.body.style.overflow = 'hidden';


        /*
        |--------------------------------------------------------------------------
        | Animasi masuk
        |--------------------------------------------------------------------------
        */

        requestAnimationFrame(() => {

            content.classList.remove(
                'scale-95',
                'opacity-0'
            );

            content.classList.add(
                'scale-100',
                'opacity-100'
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL
    |--------------------------------------------------------------------------
    */

    function closeLogoutModal() {

        const modal = document.getElementById('logout-modal');

        const content = document.getElementById(
            'logout-modal-content'
        );


        if (!modal || !content) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Animasi keluar
        |--------------------------------------------------------------------------
        */

        content.classList.remove(
            'scale-100',
            'opacity-100'
        );

        content.classList.add(
            'scale-95',
            'opacity-0'
        );


        /*
        |--------------------------------------------------------------------------
        | Sembunyikan setelah animasi selesai
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            modal.classList.remove('flex');

            modal.classList.add('hidden');

            document.body.style.overflow = '';

        }, 200);

    }


    /*
    |--------------------------------------------------------------------------
    | KONFIRMASI LOGOUT
    |--------------------------------------------------------------------------
    */

    function confirmLogout() {

        const form = document.getElementById('logout-form');


        if (form) {
            form.submit();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function(event) {

            const modal = document.getElementById(
                'logout-modal'
            );


            if (!modal) {
                return;
            }


            /*
            | Jika yang diklik adalah background overlay
            */

            if (event.target === modal) {
                closeLogoutModal();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TOMBOL ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {

                const modal = document.getElementById(
                    'logout-modal'
                );


                /*
                | Hanya tutup kalau modal sedang terbuka
                */

                if (
                    modal &&
                    !modal.classList.contains('hidden')
                ) {

                    closeLogoutModal();

                }

            }

        }
    );

</script>
