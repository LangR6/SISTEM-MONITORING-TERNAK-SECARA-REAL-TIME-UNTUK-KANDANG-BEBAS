@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Data Dashboard
    |--------------------------------------------------------------------------
    */

    $totalTernak = $summary['total'] ?? count($animals ?? []);

    $dalamKandang = $summary['inside']
        ?? $summary['dalam_kandang']
        ?? collect($animals ?? [])->filter(function ($animal) {

            $status = $animal['status'] ?? null;

            if ($status instanceof \BackedEnum) {
                $status = $status->value;
            } elseif ($status instanceof \UnitEnum) {
                $status = $status->name;
            }

            return in_array(
                strtolower((string) $status),
                ['inside', 'dalam_kandang', 'dalam kandang']
            );

        })->count();


    $keluarKandang = $summary['outside']
        ?? $summary['keluar_kandang']
        ?? collect($animals ?? [])->filter(function ($animal) {

            $status = $animal['status'] ?? null;

            if ($status instanceof \BackedEnum) {
                $status = $status->value;
            } elseif ($status instanceof \UnitEnum) {
                $status = $status->name;
            }

            return in_array(
                strtolower((string) $status),
                ['outside', 'keluar_kandang', 'keluar kandang']
            );

        })->count();


    $offline = $summary['offline']
        ?? collect($animals ?? [])->filter(function ($animal) {

            $status = $animal['status'] ?? null;

            if ($status instanceof \BackedEnum) {
                $status = $status->value;
            } elseif ($status instanceof \UnitEnum) {
                $status = $status->name;
            }

            return in_array(
                strtolower((string) $status),
                ['offline', 'mati']
            );

        })->count();

@endphp


<div class="space-y-4">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section class="flex flex-col gap-1.5 md:flex-row md:items-end md:justify-between">

        <div>

            <p class="text-[11px] font-semibold text-[#8a9aa7]">
                Monitoring Ternak
            </p>

            <h1 class="mt-0.5 text-[22px] font-extrabold tracking-[-0.03em] text-[#17334d]">
                Dashboard
            </h1>

            <p class="mt-1 text-[11px] font-medium text-[#82929f]">
                Pantau lokasi dan status ternak secara berkala.
            </p>

        </div>


        <div class="flex items-center gap-2">

            <div
                class="flex items-center gap-2 rounded-full border border-[#dfe7ec] bg-white px-3 py-2 shadow-[0_2px_8px_rgba(21,48,72,0.04)]"
            >

                <span class="size-2 rounded-full bg-[#20a66a]"></span>

                <span class="text-[10px] font-bold text-[#557082]">
                    Sistem Online
                </span>

            </div>

        </div>

    </section>



    {{-- =========================================================
        SUMMARY CARD
    ========================================================== --}}

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">


        {{-- =====================================================
            TOTAL TERNAK
        ====================================================== --}}

        <div
            class="rounded-[14px] border border-[#dfe7ec] bg-white p-4 shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
        >

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#91a0ad]">
                        Total Ternak
                    </p>

                    <p class="mt-2 text-[25px] font-extrabold leading-none text-[#17334d]">
                        {{ $totalTernak }}
                    </p>

                    <p class="mt-2 text-[10px] font-medium text-[#8b9aa6]">
                        Ternak terdaftar
                    </p>

                </div>


                <span
                    class="grid size-9 shrink-0 place-items-center rounded-[10px] bg-[#edf4fa] text-[#32627f]"
                >

                    <x-icon
                        name="cattle"
                        :size="19"
                    />

                </span>

            </div>

        </div>



        {{-- =====================================================
            DALAM KANDANG
        ====================================================== --}}

        <div
            class="rounded-[14px] border border-[#dfe7ec] bg-white p-4 shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
        >

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#91a0ad]">
                        Dalam Kandang
                    </p>

                    <p class="mt-2 text-[25px] font-extrabold leading-none text-[#17334d]">
                        {{ $dalamKandang }}
                    </p>

                    <p class="mt-2 text-[10px] font-medium text-[#8b9aa6]">
                        Lokasi aman
                    </p>

                </div>


                <span
                    class="grid size-9 shrink-0 place-items-center rounded-[10px] bg-[#e9f8f1] text-[#20a66a]"
                >

                    <x-icon
                        name="check"
                        :size="19"
                    />

                </span>

            </div>

        </div>



        {{-- =====================================================
            KELUAR KANDANG
        ====================================================== --}}

        <div
            class="rounded-[14px] border border-[#dfe7ec] bg-white p-4 shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
        >

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#91a0ad]">
                        Keluar Kandang
                    </p>

                    <p class="mt-2 text-[25px] font-extrabold leading-none text-[#17334d]">
                        {{ $keluarKandang }}
                    </p>

                    <p class="mt-2 text-[10px] font-medium text-[#8b9aa6]">
                        Perlu diperiksa
                    </p>

                </div>


                <span
                    class="grid size-9 shrink-0 place-items-center rounded-[10px] bg-[#fff0eb] text-[#e55353]"
                >

                    <x-icon
                        name="alert"
                        :size="19"
                    />

                </span>

            </div>

        </div>



        {{-- =====================================================
            OFFLINE
        ====================================================== --}}

        <div
            class="rounded-[14px] border border-[#dfe7ec] bg-white p-4 shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
        >

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#91a0ad]">
                        Offline
                    </p>

                    <p class="mt-2 text-[25px] font-extrabold leading-none text-[#17334d]">
                        {{ $offline }}
                    </p>

                    <p class="mt-2 text-[10px] font-medium text-[#8b9aa6]">
                        Perangkat tidak aktif
                    </p>

                </div>


                {{-- IKON OFFLINE --}}
                <span
                    class="grid size-9 shrink-0 place-items-center rounded-[10px] bg-[#f0f3f5] text-[#7d8d99]"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="19"
                        height="19"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >

                        <path d="M1 1l22 22"></path>

                        <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>

                        <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.89"></path>

                        <path d="M8.53 16.11a6 6 0 0 1 6.94 0"></path>

                        <path d="M12 20h.01"></path>

                    </svg>

                </span>

            </div>

        </div>

    </section>



    {{-- =========================================================
        MAP + DAFTAR TERNAK
    ========================================================== --}}

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_330px]">


        {{-- =====================================================
            MAP
        ====================================================== --}}

        <div class="min-w-0">

            <div
                class="overflow-hidden rounded-[14px] border border-[#dfe7ec] bg-white shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
            >


                {{-- HEADER MAP --}}

                <div
                    class="flex items-center justify-between border-b border-[#edf1f3] px-4 py-3"
                >

                    <div>

                        <h2 class="text-[13px] font-extrabold text-[#17334d]">
                            Lokasi Ternak
                        </h2>

                        <p class="mt-0.5 text-[10px] font-medium text-[#8b9aa6]">
                            Posisi ternak berdasarkan data GPS terakhir
                        </p>

                    </div>


                    <a
                        href="{{ url('/lokasi-ternak') }}"
                        class="inline-flex items-center gap-1.5 rounded-[8px] border border-[#dfe7ec] bg-white px-2.5 py-1.5 text-[10px] font-bold text-[#526b7c] transition hover:border-[#f2a15d] hover:text-[#e67e22]"
                    >

                        Lihat Detail

                        <x-icon
                            name="arrow-right"
                            :size="13"
                        />

                    </a>

                </div>



                {{-- MAP --}}

                <div class="p-3">

                    <x-map
                        :animals="$animals"
                        :geofence="$geofence"
                        read-only
                        :height="'h-[430px] max-[440px]:h-[375px] md:h-[430px]'"
                    />

                </div>

            </div>

        </div>



        {{-- =====================================================
            DAFTAR TERNAK
        ====================================================== --}}

        <div
            class="min-w-0 overflow-hidden rounded-[14px] border border-[#dfe7ec] bg-white shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
        >


            {{-- HEADER DAFTAR --}}

            <div class="border-b border-[#edf1f3] px-4 py-3">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <h2 class="text-[13px] font-extrabold text-[#17334d]">
                            Daftar Ternak
                        </h2>

                        <p class="mt-0.5 text-[10px] font-medium text-[#8b9aa6]">
                            Status perangkat dan lokasi
                        </p>

                    </div>


                    <span
                        class="rounded-full bg-[#f3f6f8] px-2.5 py-1 text-[9px] font-bold text-[#6d8190]"
                    >
                        {{ $totalTernak }} Ternak
                    </span>

                </div>

            </div>



            {{-- LIST TERNAK --}}

            <div class="max-h-[430px] overflow-y-auto">

                <x-animal-list
                    :animals="$animals"
                    :filters="false"
                    mode="link"
                    :limit="null"
                />

            </div>

        </div>

    </section>



    {{-- =========================================================
        INFORMASI SISTEM
    ========================================================== --}}

    <section
        class="rounded-[14px] border border-[#dfe7ec] bg-white p-4 shadow-[0_2px_8px_rgba(21,48,72,0.035)]"
    >

        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >


            {{-- INFORMASI --}}

            <div>

                <div class="flex items-center gap-2">

                    <span
                        class="grid size-8 place-items-center rounded-[9px] bg-[#edf4fa] text-[#32627f]"
                    >

                        <x-icon
                            name="info"
                            :size="17"
                        />

                    </span>


                    <h2 class="text-[12px] font-extrabold text-[#17334d]">
                        Informasi Sistem
                    </h2>

                </div>


                <p class="mt-2 text-[10px] leading-5 text-[#82929f]">
                    Data lokasi ternak diperbarui secara berkala menggunakan GPS
                    pada tracker dan dikirim melalui LoRa menuju gateway.
                </p>

            </div>



            {{-- TEKNOLOGI --}}

            <div class="flex flex-wrap items-center gap-2">


                {{-- GPS --}}

                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-[#edf4fa] px-3 py-1.5 text-[9px] font-bold text-[#527188]"
                >

                    <span class="size-1.5 rounded-full bg-[#527188]"></span>

                    GPS

                </span>



                {{-- LORA --}}

                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-[#e9f8f1] px-3 py-1.5 text-[9px] font-bold text-[#168653]"
                >

                    <span class="size-1.5 rounded-full bg-[#20a66a]"></span>

                    LoRa

                </span>



                {{-- 4G --}}

                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-[#fff4e9] px-3 py-1.5 text-[9px] font-bold text-[#c66c1c]"
                >

                    <span class="size-1.5 rounded-full bg-[#f28a32]"></span>

                    4G

                </span>

            </div>

        </div>

    </section>

</div>

@endsection
