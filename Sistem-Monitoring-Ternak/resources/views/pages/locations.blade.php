@extends('layouts.app')

@section('title', 'Lokasi Ternak')

@section('page_title', 'Lokasi Ternak')

@section('page_subtitle', 'Pantau posisi terkini seluruh ternak')

@section('content')

    @php
        /*
        |--------------------------------------------------------------------------
        | DATA TERNAK UNTUK JAVASCRIPT
        |--------------------------------------------------------------------------
        */

        $livestockData = $animals->map(function ($animal) {
            $status = $animal['status'];

            return [
                'id' => $animal['id'],
                'type' => $animal['type'],
                'lat' => $animal['lat'],
                'lng' => $animal['lng'],
                'time' => $animal['time'],
                'status' => $status->value,
                'statusLabel' => $status->label(),
            ];
        })->values();
    @endphp


    <section
        class="grid grid-cols-1 gap-[18px]"
        data-locations
    >

        {{-- =========================================================
             PETA + DAFTAR TERNAK
             ========================================================= --}}

        <div
            class="grid grid-cols-1 items-stretch gap-[18px] lg:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.68fr)] lg:grid-rows-[720px]"
        >

            {{-- =====================================================
                 PETA
                 ===================================================== --}}

            <article
                class="flex h-full min-h-0 flex-col overflow-hidden rounded-card border border-line bg-white shadow-card"
            >

                <div
                    class="flex min-h-[72px] shrink-0 items-center justify-between gap-4 px-3.5 py-3.5 md:px-5 md:py-4"
                >

                    <div>

                        <h2
                            class="m-0 text-sm font-extrabold text-navy-900"
                        >
                            Peta Lokasi Saat Ini
                        </h2>

                        <p
                            class="mt-[5px] text-[11px] text-muted"
                        >
                            Menampilkan posisi terkini seluruh ternak
                        </p>

                    </div>


                    <span
                        class="flex shrink-0 items-center gap-1.5 rounded-md bg-inside-soft px-2 py-1.5 text-[11px] font-bold text-[#168153]"
                    >

                        <i
                            class="size-1.5 rounded-full bg-inside shadow-[0_0_0_3px_rgba(32,166,106,0.15)]"
                        ></i>

                        Data terkini

                    </span>

                </div>


                {{-- MAP --}}

                <div class="min-h-0 shrink-0">

                    <x-map
                        :animals="$animals"
                        :geofence="$geofence"
                        :selected="$selected['id']"
                        height="h-full min-h-[430px] max-[440px]:min-h-[375px]"
                    />

                </div>


                {{-- INFORMASI GPS (di dalam kotak peta) --}}

        <div
            class="shrink-0 border-t border-line"
            data-detail
            data-status="{{ $selected['status']->value }}"
        >

            <div class="px-5 py-3.5">

                {{-- HEADER --}}

                <div
                    class="mb-2.5 flex items-center justify-between gap-3"
                >

                    <div>

                        <p
                            class="mb-1 text-[10px] font-extrabold tracking-[0.14em] text-orange"
                        >
                            INFORMASI GPS
                        </p>

                        <h3
                            class="m-0 text-sm font-extrabold text-navy-900"
                        >
                            Detail Ternak
                        </h3>

                    </div>

                </div>


                {{-- =================================================
                     IDENTITAS TERNAK
                     ================================================= --}}

                <div
                    class="flex flex-wrap items-center gap-3 rounded-[10px] bg-[#f7f9fb] p-3"
                >

                    {{-- ICON --}}

                    <span
                        class="grid size-[46px] shrink-0 place-items-center rounded-[9px] bg-(--tone-soft) text-(--tone)"
                        data-detail-icon-wrapper
                    >

                        {{-- ICON SAPI --}}

                        <span
                            data-detail-icon="Sapi"
                            @if ($selected['type'] !== 'Sapi')
                                hidden
                            @endif
                        >

                            <x-icon
                                name="cattle"
                                :size="27"
                            />

                        </span>


                        {{-- ICON KAMBING --}}

                        <span
                            data-detail-icon="Kambing"
                            @if ($selected['type'] !== 'Kambing')
                                hidden
                            @endif
                        >

                            <x-icon
                                name="goat"
                                :size="27"
                            />

                        </span>

                    </span>


                    {{-- IDENTITAS --}}

                    <div
                        class="flex min-w-[150px] flex-1 flex-col"
                    >

                        <strong
                            data-detail-id
                            class="whitespace-nowrap text-xs text-navy-900"
                        >
                            {{ $selected['id'] }}
                        </strong>

                        <span
                            data-detail-type
                            class="mt-[3px] text-[11px] text-muted"
                        >
                            {{ $selected['type'] }}
                        </span>

                    </div>


                    {{-- STATUS --}}

                    <div
                        data-detail-status-wrapper
                    >

                        <x-status-badge
                            :status="$selected['status']"
                        />

                    </div>

                </div>


                {{-- =================================================
                     DATA GPS
                     ================================================= --}}

                <dl
                    class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4"
                >

                    {{-- LATITUDE --}}

                    <div
                        class="flex flex-col border-l-2 border-line pl-[11px]"
                    >

                        <dt
                            class="text-[11px] text-muted"
                        >
                            Latitude
                        </dt>

                        <dd
                            data-detail-field="lat"
                            class="m-0 mt-[5px] text-xs font-bold text-navy-900"
                        >
                            {{ $selected['lat'] }}
                        </dd>

                    </div>


                    {{-- LONGITUDE --}}

                    <div
                        class="flex flex-col border-l-2 border-line pl-[11px]"
                    >

                        <dt
                            class="text-[11px] text-muted"
                        >
                            Longitude
                        </dt>

                        <dd
                            data-detail-field="lng"
                            class="m-0 mt-[5px] text-xs font-bold text-navy-900"
                        >
                            {{ $selected['lng'] }}
                        </dd>

                    </div>


                    {{-- TERAKHIR UPDATE --}}

                    <div
                        class="flex flex-col border-l-2 border-line pl-[11px]"
                    >

                        <dt
                            class="text-[11px] text-muted"
                        >
                            Terakhir Update
                        </dt>

                        <dd
                            data-detail-field="time"
                            class="m-0 mt-[5px] text-xs font-bold text-navy-900"
                        >
                            {{ $selected['time'] }} WIB
                        </dd>

                    </div>


                    {{-- SUMBER DATA --}}

                    <div
                        class="flex flex-col border-l-2 border-line pl-[11px]"
                    >

                        <dt
                            class="text-[11px] text-muted"
                        >
                            Sumber Data
                        </dt>

                        <dd
                            class="m-0 mt-[5px] text-xs font-bold text-navy-900"
                        >
                            GPS Tracker
                        </dd>

                    </div>

                </dl>

            </div>

        </div>

            </article>


            {{-- =====================================================
                 DAFTAR TERNAK
                 ===================================================== --}}

            <article
                class="flex h-full min-h-0 flex-col overflow-hidden rounded-card border border-line bg-white shadow-card"
            >

                <div
                    class="flex min-h-[72px] shrink-0 items-center justify-between gap-4 border-b border-line px-[18px] py-4"
                >

                    <div>

                        <h2
                            class="m-0 text-sm font-extrabold text-navy-900"
                        >
                            Daftar Ternak
                        </h2>

                        <p
                            class="mt-[5px] text-[11px] text-muted"
                        >
                            {{ $summary['total'] }} ternak terdaftar
                        </p>

                    </div>

                </div>


                <div
                    class="min-h-0 flex-1 overflow-hidden"
                >

                    <x-animal-list
                        :animals="$animals"
                        :selected="$selected['id']"
                    />

                </div>

            </article>

        </div>


    </section>


    {{-- =============================================================
         DATA TERNAK
         ============================================================= --}}

    <script
        type="application/json"
        id="livestock-data"
    >{!! json_encode($livestockData) !!}</script>


    {{-- =============================================================
         JAVASCRIPT DETAIL TERNAK
         ============================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA
            |--------------------------------------------------------------------------
            */

            const dataElement =
                document.getElementById('livestock-data');

            const detail =
                document.querySelector('[data-detail]');


            if (!dataElement) {

                console.error(
                    'Element #livestock-data tidak ditemukan.'
                );

                return;
            }


            if (!detail) {

                console.error(
                    'Element [data-detail] tidak ditemukan.'
                );

                return;
            }


            let animals = [];


            try {

                animals = JSON.parse(
                    dataElement.textContent
                );

            } catch (error) {

                console.error(
                    'Data livestock gagal dibaca:',
                    error
                );

                return;
            }


            console.log(
                'DATA TERNAK:',
                animals
            );


            /*
            |--------------------------------------------------------------------------
            | CARI TERNAK BERDASARKAN ID
            |--------------------------------------------------------------------------
            */

            function findAnimal(id) {

                if (!id) {
                    return null;
                }


                const searchId =
                    String(id).trim();


                return animals.find(function (animal) {

                    return String(animal.id).trim()
                        === searchId;

                }) || null;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            function updateStatus(animal) {

                const wrapper =
                    detail.querySelector(
                        '[data-detail-status-wrapper]'
                    );


                if (!wrapper) {
                    return;
                }


                const status =
                    String(
                        animal.status || ''
                    ).toLowerCase();


                const label =
                    animal.statusLabel ||
                    animal.status ||
                    '-';


                let classes =
                    'inline-flex items-center rounded-md px-2.5 py-1.5 text-[11px] font-bold';


                if (status === 'inside') {

                    classes +=
                        ' bg-inside-soft text-[#168153]';

                } else if (status === 'outside') {

                    classes +=
                        ' bg-orange-50 text-[#d66f18]';

                } else {

                    classes +=
                        ' bg-gray-100 text-gray-500';

                }


                wrapper.innerHTML = `
                    <span class="${classes}">
                        ${label}
                    </span>
                `;


                detail.dataset.status =
                    animal.status || '';

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE ICON
            |--------------------------------------------------------------------------
            */

            function updateIcon(animal) {

                const cattleIcon =
                    detail.querySelector(
                        '[data-detail-icon="Sapi"]'
                    );


                const goatIcon =
                    detail.querySelector(
                        '[data-detail-icon="Kambing"]'
                    );


                if (cattleIcon) {

                    cattleIcon.hidden =
                        animal.type !== 'Sapi';

                }


                if (goatIcon) {

                    goatIcon.hidden =
                        animal.type !== 'Kambing';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE SEMUA DETAIL
            |--------------------------------------------------------------------------
            */

            function updateDetail(id) {

                console.log(
                    'Memperbarui detail untuk:',
                    id
                );


                const animal =
                    findAnimal(id);


                if (!animal) {

                    console.error(
                        'Ternak dengan ID tersebut tidak ditemukan:',
                        id
                    );

                    return;
                }


                console.log(
                    'Ternak ditemukan:',
                    animal
                );


                /*
                |--------------------------------------------------------------------------
                | ID
                |--------------------------------------------------------------------------
                */

                const idElement =
                    detail.querySelector(
                        '[data-detail-id]'
                    );


                if (idElement) {

                    idElement.textContent =
                        animal.id || '-';

                }


                /*
                |--------------------------------------------------------------------------
                | JENIS
                |--------------------------------------------------------------------------
                */

                const typeElement =
                    detail.querySelector(
                        '[data-detail-type]'
                    );


                if (typeElement) {

                    typeElement.textContent =
                        animal.type || '-';

                }


                /*
                |--------------------------------------------------------------------------
                | LATITUDE
                |--------------------------------------------------------------------------
                */

                const latitudeElement =
                    detail.querySelector(
                        '[data-detail-field="lat"]'
                    );


                if (latitudeElement) {

                    latitudeElement.textContent =
                        animal.lat ?? '-';

                }


                /*
                |--------------------------------------------------------------------------
                | LONGITUDE
                |--------------------------------------------------------------------------
                */

                const longitudeElement =
                    detail.querySelector(
                        '[data-detail-field="lng"]'
                    );


                if (longitudeElement) {

                    longitudeElement.textContent =
                        animal.lng ?? '-';

                }


                /*
                |--------------------------------------------------------------------------
                | WAKTU UPDATE
                |--------------------------------------------------------------------------
                */

                const timeElement =
                    detail.querySelector(
                        '[data-detail-field="time"]'
                    );


                if (timeElement) {

                    if (animal.time) {

                        timeElement.textContent =
                            animal.time + ' WIB';

                    } else {

                        timeElement.textContent =
                            '-';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                updateStatus(animal);


                /*
                |--------------------------------------------------------------------------
                | ICON
                |--------------------------------------------------------------------------
                */

                updateIcon(animal);

            }


            /*
            |--------------------------------------------------------------------------
            | KLIK DAFTAR TERNAK
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const row =
                        event.target.closest(
                            '[data-animal-row]'
                        );


                    if (!row) {
                        return;
                    }


                    const animalId =
                        row.getAttribute(
                            'data-id'
                        );


                    if (!animalId) {

                        console.warn(
                            'Baris ternak tidak mempunyai data-id.'
                        );

                        return;
                    }


                    console.log(
                        'DAFTAR TERNAK DIKLIK:',
                        animalId
                    );


                    updateDetail(
                        animalId
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | EVENT DARI MAP
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'animal:selected',
                function (event) {

                    const animalId =
                        event.detail &&
                        event.detail.id;


                    if (!animalId) {
                        return;
                    }


                    console.log(
                        'EVENT animal:selected:',
                        animalId
                    );


                    updateDetail(
                        animalId
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | DETAIL AWAL
            |--------------------------------------------------------------------------
            */

            const initialId =
                detail
                    .querySelector(
                        '[data-detail-id]'
                    )
                    ?.textContent
                    ?.trim();


            if (initialId) {

                updateDetail(
                    initialId
                );

            }

        });
    </script>

@endsection