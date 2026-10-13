
@php
    use App\Enums\LivestockStatus;
    use App\Support\LivestockRepository;

    $user = \App\Support\DemoUser::current();

    $notificationAnimals = app(LivestockRepository::class)
        ->all()
        ->filter(function ($animal) {
            return $animal['status'] === LivestockStatus::Outside;
        })
        ->values();

    $notificationCount = $notificationAnimals->count();
@endphp

<header class="sticky top-0 z-20 flex h-[72px] items-center border-b border-line bg-white/95 px-4 backdrop-blur md:h-[82px] md:px-[31px]">

    {{-- MOBILE SIDEBAR BUTTON --}}
    <button
        type="button"
        data-sidebar-open
        aria-label="Buka menu"
        class="mr-[13px] grid size-[38px] place-items-center rounded-[9px] border border-line bg-white text-navy-900 md:hidden"
    >
        <x-icon name="menu" />
    </button>

    {{-- PAGE TITLE --}}
    <div class="min-w-0">
        <h1 class="m-0 text-base font-extrabold tracking-tight text-navy-900 md:text-[22px]">
            @yield('page_title', 'Dashboard Monitoring')
        </h1>

        <p class="mt-1 max-w-[180px] truncate text-[11px] text-muted md:max-w-none md:text-xs">
            @yield('page_subtitle')
        </p>
    </div>

    {{-- RIGHT HEADER --}}
    <div class="ml-auto flex items-center gap-1.5 md:gap-[18px]">

        {{-- NOTIFICATION MENU --}}
        <div class="relative" data-notification-menu>

            <button
                type="button"
                data-notification-trigger
                aria-label="Notifikasi"
                aria-expanded="false"
                aria-haspopup="menu"
                class="relative grid size-9 place-items-center rounded-[10px] border border-line bg-canvas text-[#526b80] transition hover:border-[#d6dee4] hover:bg-white hover:text-navy-900 md:size-10"
            >
                <x-icon name="bell" :size="18" />

                @if ($notificationCount > 0)
                    <span
                        data-notification-badge
                        class="absolute -right-[3px] -top-[4px] flex h-[17px] min-w-[17px] items-center justify-center rounded-full border-2 border-white bg-[#e05252] px-1 text-[8px] font-extrabold leading-none text-white"
                    >
                        {{ $notificationCount }}
                    </span>
                @else
                    <span
                        data-notification-badge
                        hidden
                        class="absolute -right-[3px] -top-[4px] h-[17px] min-w-[17px] items-center justify-center rounded-full border-2 border-white bg-[#e05252] px-1 text-[8px] font-extrabold leading-none text-white"
                    >
                        0
                    </span>
                @endif
            </button>

            {{-- NOTIFICATION PANEL --}}
            <div
                data-notification-panel
                hidden
                role="menu"
                aria-label="Daftar notifikasi ternak"
                class="absolute right-0 top-[calc(100%+12px)] z-50 w-[330px] overflow-hidden rounded-[14px] border border-line bg-white shadow-[0_16px_40px_rgba(10,34,57,0.18)] sm:w-[360px]"
            >
                <div class="flex items-center justify-between border-b border-line px-4 py-3.5">
                    <div>
                        <h2 class="m-0 text-[13px] font-extrabold text-navy-900">
                            Notifikasi
                        </h2>
                        <p class="mt-1 text-[10px] text-muted">
                            Ternak yang berada di luar kandang
                        </p>
                    </div>

                    @if ($notificationCount > 0)
                        <span class="rounded-full bg-[#fff0ed] px-2 py-1 text-[9px] font-extrabold text-[#d94b3d]">
                            {{ $notificationCount }} ternak
                        </span>
                    @endif
                </div>

                <div class="max-h-[380px] overflow-y-auto" data-notification-list>
                    @if ($notificationCount > 0)
                        @foreach ($notificationAnimals as $animal)
                            <a
                                role="menuitem"
                                href="{{ route('locations', ['ternak' => $animal['id']]) }}"
                                data-notification-item
                                data-animal-id="{{ $animal['id'] }}"
                                class="group flex items-start gap-3 border-b border-[#edf1f4] px-4 py-3.5 no-underline transition last:border-b-0 hover:bg-[#fff8f5]"
                            >
                                <span class="grid size-[38px] shrink-0 place-items-center rounded-[10px] bg-[#fff0ed] text-[#e05252]">
                                    @if ($animal['type'] === 'Kambing')
                                        <x-icon name="goat" :size="20" />
                                    @else
                                        <x-icon name="cattle" :size="20" />
                                    @endif
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center justify-between gap-2">
                                        <strong class="truncate text-[11px] font-extrabold text-navy-900">
                                            {{ $animal['id'] }}
                                        </strong>

                                        <span class="shrink-0 text-[9px] font-medium text-muted">
                                            {{ $animal['time'] }} WIB
                                        </span>
                                    </span>

                                    <span class="mt-1 block text-[10px] text-muted">
                                        {{ $animal['type'] }} ·
                                        <span class="font-bold text-[#e05252]">
                                            Keluar kandang
                                        </span>
                                    </span>

                                    <span class="mt-1.5 flex items-center gap-1 text-[9px] text-[#8493a0]">
                                        <span class="size-1.5 rounded-full bg-[#e05252]"></span>
                                        Terdeteksi di luar area geofence
                                    </span>
                                </span>

                                <span class="mt-1 shrink-0 text-[#9aa7b2] transition group-hover:translate-x-0.5 group-hover:text-orange">
                                    <x-icon name="chevron" :size="13" class="rotate-90" />
                                </span>
                            </a>
                        @endforeach
                    @else
                        <div class="flex flex-col items-center justify-center px-5 py-10 text-center">
                            <span class="grid size-11 place-items-center rounded-full bg-[#eaf8f1] text-[#1baa68]">
                                <x-icon name="check" :size="21" />
                            </span>

                            <strong class="mt-3 text-xs font-extrabold text-navy-900">
                                Semua ternak aman
                            </strong>

                            <p class="mt-1 max-w-[220px] text-[10px] leading-4 text-muted">
                                Saat ini tidak ada ternak yang terdeteksi berada di luar kandang.
                            </p>
                        </div>
                    @endif
                </div>

                @if ($notificationCount > 0)
                    <div class="border-t border-line bg-[#fafbfc] px-4 py-2.5">
                        <a
                            href="{{ route('locations') }}"
                            class="flex items-center justify-center gap-1.5 text-[10px] font-extrabold text-[#526b80] no-underline transition hover:text-orange"
                        >
                            Lihat semua lokasi
                            <x-icon name="chevron" :size="11" class="rotate-90" />
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- PROFILE MENU --}}
        <div class="relative" data-profile-menu>

            <button
                type="button"
                data-profile-trigger
                aria-expanded="false"
                aria-haspopup="menu"
                aria-label="Buka menu profil"
                class="group flex items-center gap-2.5 border-0 bg-transparent p-0 text-left md:border-l md:border-line md:pl-[18px]"
            >
                <span class="grid size-[34px] place-items-center rounded-[10px] bg-navy-800 text-white md:size-9">
                    <x-icon name="user" :size="18" />
                </span>

                <div class="hidden min-w-[104px] flex-col md:flex">
                    <strong class="text-xs">
                        {{ $user['name'] }}
                    </strong>
                    <small class="mt-0.5 text-[11px] text-muted">
                        {{ $user['role'] }}
                    </small>
                </div>

                <x-icon
                    name="chevron"
                    :size="14"
                    class="hidden rotate-90 text-muted transition group-aria-expanded:-rotate-90 md:block"
                />
            </button>

            {{-- PROFILE PANEL --}}
            <div
                data-profile-panel
                hidden
                role="menu"
                aria-label="Menu profil"
                class="absolute right-0 top-[calc(100%+13px)] z-50 w-[220px] rounded-[11px] border border-line bg-white p-[7px] shadow-[0_14px_35px_rgba(10,34,57,0.16)]"
            >
                <div class="flex flex-col border-b border-line px-2.5 pb-[11px] pt-[9px]">
                    <strong class="text-xs">
                        {{ $user['name'] }}
                    </strong>
                    <small class="mt-1 truncate text-[11px] text-muted">
                        {{ $user['email'] }}
                    </small>
                </div>

                <a
                    role="menuitem"
                    href="{{ route('settings', ['tab' => 'profile']) }}"
                    class="mt-[3px] flex w-full items-center gap-[9px] rounded-[7px] px-2.5 py-[9px] text-xs font-semibold text-[#526a7d] hover:bg-canvas hover:text-navy-900"
                >
                    <x-icon name="user" :size="16" />
                    Ubah Data Diri
                </a>

                <a
                    role="menuitem"
                    href="{{ route('settings', ['tab' => 'password']) }}"
                    class="mt-[3px] flex w-full items-center gap-[9px] rounded-[7px] px-2.5 py-[9px] text-xs font-semibold text-[#526a7d] hover:bg-canvas hover:text-navy-900"
                >
                    <x-icon name="settings" :size="16" />
                    Ubah Password
                </a>
            </div>
        </div>
    </div>
</header>

{{-- HEADER DROPDOWN JAVASCRIPT --}}
<script>
(function () {
    if (window.__livestockHeaderMenusInitialized) {
        return;
    }

    window.__livestockHeaderMenusInitialized = true;

    const initHeaderMenus = function () {
        const notificationMenu = document.querySelector('[data-notification-menu]');
        const notificationTrigger = document.querySelector('[data-notification-trigger]');
        const notificationPanel = document.querySelector('[data-notification-panel]');

        const profileMenu = document.querySelector('[data-profile-menu]');
        const profileTrigger = document.querySelector('[data-profile-trigger]');
        const profilePanel = document.querySelector('[data-profile-panel]');

        function closeNotification() {
            notificationPanel?.setAttribute('hidden', '');
            notificationTrigger?.setAttribute('aria-expanded', 'false');
        }

        function closeProfile() {
            profilePanel?.setAttribute('hidden', '');
            profileTrigger?.setAttribute('aria-expanded', 'false');
        }

        notificationTrigger?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (notificationPanel?.hasAttribute('hidden')) {
                closeProfile();
                notificationPanel.removeAttribute('hidden');
                notificationTrigger.setAttribute('aria-expanded', 'true');
            } else {
                closeNotification();
            }
        });

        profileTrigger?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (profilePanel?.hasAttribute('hidden')) {
                closeNotification();
                profilePanel.removeAttribute('hidden');
                profileTrigger.setAttribute('aria-expanded', 'true');
            } else {
                closeProfile();
            }
        });

        document.addEventListener('click', function (event) {
            if (notificationMenu && !notificationMenu.contains(event.target)) {
                closeNotification();
            }

            if (profileMenu && !profileMenu.contains(event.target)) {
                closeProfile();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeNotification();
                closeProfile();
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeaderMenus, { once: true });
    } else {
        initHeaderMenus();
    }
})();
</script>