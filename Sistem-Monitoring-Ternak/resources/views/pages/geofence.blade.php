@extends('layouts.app')

@section('title', 'Pengaturan Geofence')

@section('page_title', 'Pengaturan Geofence')

@section('page_subtitle', 'Atur batas aman area kandang')

@section('content')

<form
    method="POST"
    action="{{ route('geofence.update') }}"
    data-geofence-editor
    class="grid grid-cols-1 gap-[18px] lg:grid-cols-[minmax(0,1.6fr)_320px]"
    novalidate
>
    @csrf

    {{-- ========================================================= --}}
    {{-- MAP --}}
    {{-- ========================================================= --}}

    <article class="overflow-hidden rounded-card border border-line bg-white shadow-card">

        <div class="flex min-h-[72px] flex-col items-start justify-between gap-4 px-3.5 py-3.5 md:flex-row md:items-center md:px-5 md:py-4">

            <div>
                <h2 class="m-0 text-sm font-extrabold text-navy-900">
                    Tentukan Area Geofence
                </h2>

                <p class="mt-[5px] text-[11px] text-muted">
                    Ambil titik menggunakan GPS HP untuk membentuk batas area kandang
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                {{-- PETA BIASA --}}
                <button
                    type="button"
                    id="streetMapBtn"
                    class="inline-flex min-h-[34px] items-center justify-center gap-[7px] rounded-lg border border-[#d6e0e8] bg-white px-3 text-[11px] font-bold text-navy-700 hover:bg-[#f7f9fb]"
                >
                    Peta
                </button>

                {{-- SATELIT --}}
                <button
                    type="button"
                    id="satelliteMapBtn"
                    class="inline-flex min-h-[34px] items-center justify-center gap-[7px] rounded-lg border border-[#d6e0e8] bg-white px-3 text-[11px] font-bold text-navy-700 hover:bg-[#f7f9fb]"
                >
                    Satelit
                </button>

                {{-- GPS --}}
                <button
                    type="button"
                    data-add-current-point
                    id="getLocationBtn"
                    class="inline-flex min-h-[34px] items-center justify-center gap-[7px] rounded-lg border border-orange bg-orange px-3 text-[11px] font-bold text-white hover:bg-orange-dark disabled:opacity-50"
                >
                    <x-icon name="crosshair" :size="17" />

                    <span id="locationButtonText">
                        Ambil Titik Koordinat
                    </span>
                </button>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- LEAFLET MAP --}}
        {{-- ===================================================== --}}

        <div
            id="geofence-map"
            class="h-[430px] w-full max-[440px]:h-[375px] md:h-[540px]"
        ></div>


        {{-- ===================================================== --}}
        {{-- MAP LEGEND --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col gap-2 border-t border-line px-4 py-3 md:flex-row md:items-center">

            <div class="flex items-center gap-4 text-[11px]">

                <div class="flex items-center gap-1.5">
                    <span class="size-3 rounded-full bg-blue-600"></span>
                    <span class="text-muted">Titik Geofence</span>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="size-3 rounded-full bg-orange"></span>
                    <span class="text-muted">Lokasi HP</span>
                </div>

            </div>

            <span class="text-[11px] font-medium text-[#6a7e8e] md:ml-auto">
                Klik peta untuk menambahkan titik atau gunakan GPS HP.
            </span>

        </div>

    </article>


    {{-- ========================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================================= --}}

    <aside
        class="overflow-hidden rounded-card border border-line bg-white pb-4 shadow-card md:grid md:grid-cols-2 md:gap-x-3 lg:block"
    >

        {{-- HEADER --}}
        <div class="min-h-[72px] border-b border-line px-[18px] py-4 md:col-span-full">

            <h2 class="m-0 text-sm font-extrabold text-navy-900">
                Detail Area
            </h2>

            <p class="mt-[5px] text-[11px] text-muted">
                <span data-point-count>
                    {{ count($geofence['points']) }}
                </span>
                titik koordinat
            </p>

        </div>


        {{-- ===================================================== --}}
        {{-- NAMA AREA --}}
        {{-- ===================================================== --}}

        <div>

            <label
                for="area-name"
                class="mx-[17px] mb-[7px] mt-4 block text-xs font-bold text-navy-900"
            >
                Nama Area
            </label>

            <input
                id="area-name"
                name="name"
                type="text"
                maxlength="80"
                value="{{ old('name', $geofence['name']) }}"
                data-area-name-input
                class="mx-[17px] h-[38px] w-[calc(100%-34px)] rounded-lg border border-[#dce4ea] bg-[#fafcfd] px-[11px] text-xs text-navy-900"
            >

            @error('name')
                <p class="mx-[17px] mt-1.5 text-[11px] text-outside">
                    {{ $message }}
                </p>
            @enderror


            {{-- ================================================= --}}
            {{-- DAFTAR KOORDINAT --}}
            {{-- ================================================= --}}

            <div class="mx-[17px] mb-2 mt-[18px] flex justify-between">

                <strong class="text-xs">
                    Daftar Koordinat
                </strong>

                <span class="text-[11px] text-muted">
                    Minimum 3 titik
                </span>

            </div>


            <div
                data-point-list
                class="mx-2.5 max-h-[330px] overflow-y-auto md:max-h-[275px] lg:max-h-[330px]"
            ></div>

        </div>


        {{-- ===================================================== --}}
        {{-- STATUS + SAVE --}}
        {{-- ===================================================== --}}

        <div class="px-[17px] pt-2 md:pt-7 lg:pt-2">

            <p
                data-validity
                data-valid="true"
                class="group mb-3 mt-2 flex items-center gap-[7px] text-[11px] text-outside data-[valid=true]:text-inside"
            >

                <span
                    data-validity-icon
                    class="grid size-[15px] place-items-center rounded-full bg-outside text-[10px] font-bold text-white group-data-[valid=true]:bg-inside"
                >
                    ✓
                </span>

                <span data-validity-text>
                    Area siap disimpan
                </span>

            </p>


            @error('points')
                <p class="mb-2 text-[11px] text-outside">
                    {{ $message }}
                </p>
            @enderror


            {{-- HIDDEN POINTS --}}
            <input
                type="hidden"
                name="points"
                data-points-input
            >


            {{-- SAVE --}}
            <button
                type="submit"
                data-submit
                class="mt-2 inline-flex min-h-[34px] w-full items-center justify-center gap-[7px] rounded-lg border border-orange bg-orange px-3 text-[11px] font-bold text-white hover:bg-orange-dark disabled:opacity-50"
            >
                Selesai &amp; Simpan Area
            </button>


            @if (session('status'))

                <p
                    role="status"
                    data-flash
                    class="mt-3 text-[11px] font-bold text-inside"
                >
                    ✓ {{ session('status') }}
                </p>

            @endif

        </div>

    </aside>

</form>


{{-- ============================================================= --}}
{{-- LEAFLET CSS --}}
{{-- ============================================================= --}}

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>


<style>
    #geofence-map {
        z-index: 1;
    }

    .leaflet-container {
        font-family: inherit;
    }

    .gps-popup {
        font-size: 12px;
        line-height: 1.6;
    }

    .point-number-icon {
        background: transparent;
        border: none;
    }

    .point-number-icon div {
        width: 30px;
        height: 30px;
        border-radius: 9999px;
        background: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,.25);
    }
</style>


{{-- ============================================================= --}}
{{-- LEAFLET JS --}}
{{-- ============================================================= --}}

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | DATA AWAL DARI LARAVEL
    |--------------------------------------------------------------------------
    */

    let points = @json($geofence['points'] ?? []);

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const mapElement =
        document.getElementById('geofence-map');

    const getLocationBtn =
        document.getElementById('getLocationBtn');

    const locationButtonText =
        document.getElementById('locationButtonText');

    const streetMapBtn =
        document.getElementById('streetMapBtn');

    const satelliteMapBtn =
        document.getElementById('satelliteMapBtn');

    const pointList =
        document.querySelector('[data-point-list]');

    const pointCount =
        document.querySelector('[data-point-count]');

    const pointsInput =
        document.querySelector('[data-points-input]');

    const validity =
        document.querySelector('[data-validity]');

    const validityText =
        document.querySelector('[data-validity-text]');

    const validityIcon =
        document.querySelector('[data-validity-icon]');

    const form =
        document.querySelector('[data-geofence-editor]');


    /*
    |--------------------------------------------------------------------------
    | DEFAULT LOCATION
    |--------------------------------------------------------------------------
    |
    | Hanya sebagai posisi awal peta.
    | BUKAN koordinat GPS pengguna.
    |
    */

    const defaultCenter = [
        1.1300,
        104.0500
    ];


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE MAP
    |--------------------------------------------------------------------------
    */

    const map =
        L.map(mapElement).setView(
            defaultCenter,
            13
        );


    /*
    |--------------------------------------------------------------------------
    | STREET MAP
    |--------------------------------------------------------------------------
    */

    const streetLayer =
        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,

                attribution:
                    '&copy; OpenStreetMap contributors'
            }
        );


    /*
    |--------------------------------------------------------------------------
    | SATELLITE MAP
    |--------------------------------------------------------------------------
    */

    const satelliteLayer =
        L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            {
                maxZoom: 19,

                attribution:
                    'Tiles &copy; Esri'
            }
        );


    /*
    |--------------------------------------------------------------------------
    | DEFAULT STREET
    |--------------------------------------------------------------------------
    */

    streetLayer.addTo(map);


    /*
    |--------------------------------------------------------------------------
    | LAYERS
    |--------------------------------------------------------------------------
    */

    let polygon = null;

    let gpsMarker = null;

    let pointMarkers = [];


    /*
    |--------------------------------------------------------------------------
    | MAP TYPE - STREET
    |--------------------------------------------------------------------------
    */

    streetMapBtn.addEventListener(
        'click',
        function () {

            if (map.hasLayer(satelliteLayer)) {
                map.removeLayer(satelliteLayer);
            }

            streetLayer.addTo(map);

            streetMapBtn.classList.add(
                'bg-[#f7f9fb]'
            );

            satelliteMapBtn.classList.remove(
                'bg-[#f7f9fb]'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | MAP TYPE - SATELLITE
    |--------------------------------------------------------------------------
    */

    satelliteMapBtn.addEventListener(
        'click',
        function () {

            if (map.hasLayer(streetLayer)) {
                map.removeLayer(streetLayer);
            }

            satelliteLayer.addTo(map);

            satelliteMapBtn.classList.add(
                'bg-[#f7f9fb]'
            );

            streetMapBtn.classList.remove(
                'bg-[#f7f9fb]'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | GPS HP
    |--------------------------------------------------------------------------
    */

    getLocationBtn.addEventListener(
        'click',
        function () {

            if (!navigator.geolocation) {

                alert(
                    'Browser ini tidak mendukung GPS.'
                );

                return;
            }


            getLocationBtn.disabled = true;

            locationButtonText.textContent =
                'Mengambil lokasi...';


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    const latitude =
                        position.coords.latitude;

                    const longitude =
                        position.coords.longitude;

                    const accuracy =
                        position.coords.accuracy;


                    /*
                    |--------------------------------------------------
                    | MARKER GPS HP
                    |--------------------------------------------------
                    */

                    if (gpsMarker) {

                        map.removeLayer(
                            gpsMarker
                        );

                    }


                    gpsMarker =
                        L.circleMarker(
                            [
                                latitude,
                                longitude
                            ],
                            {
                                radius: 9,

                                color: '#ffffff',

                                weight: 3,

                                fillColor: '#f97316',

                                fillOpacity: 1
                            }
                        ).addTo(map);


                    gpsMarker.bindPopup(
                        `
                        <div class="gps-popup">
                            <strong>Lokasi HP</strong><br>
                            Latitude:
                            ${latitude.toFixed(7)}
                            <br>
                            Longitude:
                            ${longitude.toFixed(7)}
                            <br>
                            Akurasi:
                            ±${Math.round(accuracy)} meter
                        </div>
                        `
                    ).openPopup();


                    /*
                    |--------------------------------------------------
                    | PINDAHKAN PETA
                    |--------------------------------------------------
                    */

                    map.setView(
                        [
                            latitude,
                            longitude
                        ],
                        18
                    );


                    /*
                    |--------------------------------------------------
                    | OTOMATIS TAMBAHKAN TITIK
                    |--------------------------------------------------
                    |
                    | Sesuai kebutuhan:
                    | tekan Ambil Titik Koordinat
                    | -> GPS membaca lokasi
                    | -> langsung menjadi Titik 1, 2, 3...
                    |
                    */

                    points.push({
                        lat: Number(
                            latitude.toFixed(7)
                        ),

                        lng: Number(
                            longitude.toFixed(7)
                        )
                    });


                    renderPoints();

                    drawPolygon();

                    updateFormData();


                    /*
                    |--------------------------------------------------
                    | TAMPILKAN AKURASI
                    |--------------------------------------------------
                    */

                    locationButtonText.textContent =
                        'Titik berhasil ditambahkan';


                    setTimeout(
                        function () {

                            locationButtonText.textContent =
                                'Ambil Titik Koordinat';

                        },
                        1500
                    );


                    getLocationBtn.disabled = false;

                },


                function (error) {

                    let message =
                        'Gagal mendapatkan lokasi GPS.';


                    if (
                        error.code ===
                        error.PERMISSION_DENIED
                    ) {

                        message =
                            'Izin lokasi ditolak. Aktifkan izin lokasi untuk browser.';

                    } else if (
                        error.code ===
                        error.POSITION_UNAVAILABLE
                    ) {

                        message =
                            'Lokasi GPS tidak tersedia. Pastikan GPS HP aktif.';

                    } else if (
                        error.code ===
                        error.TIMEOUT
                    ) {

                        message =
                            'GPS terlalu lama merespons. Silakan coba lagi.';

                    }


                    alert(message);


                    locationButtonText.textContent =
                        'Ambil Titik Koordinat';

                    getLocationBtn.disabled = false;

                },

                {
                    enableHighAccuracy: true,

                    timeout: 15000,

                    maximumAge: 0

                }

            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | KLIK PETA
    |--------------------------------------------------------------------------
    |
    | User juga tetap bisa menambahkan titik manual
    | dengan klik pada peta.
    |
    */

    map.on(
        'click',
        function (event) {

            points.push({

                lat: Number(
                    event.latlng.lat.toFixed(7)
                ),

                lng: Number(
                    event.latlng.lng.toFixed(7)
                )

            });


            renderPoints();

            drawPolygon();

            updateFormData();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RENDER POINTS
    |--------------------------------------------------------------------------
    */

    function renderPoints()
    {

        /*
        |--------------------------------------------------------------
        | HAPUS MARKER LAMA
        |--------------------------------------------------------------
        */

        pointMarkers.forEach(
            function (marker) {

                map.removeLayer(marker);

            }
        );

        pointMarkers = [];


        pointList.innerHTML = '';


        /*
        |--------------------------------------------------------------
        | BUAT MARKER + LIST
        |--------------------------------------------------------------
        */

        points.forEach(
            function (point, index) {

                const number =
                    index + 1;


                /*
                |------------------------------------------------------
                | MARKER NOMOR
                |------------------------------------------------------
                */

                const icon =
                    L.divIcon({

                        className:
                            'point-number-icon',

                        html:
                            `<div>${number}</div>`,

                        iconSize:
                            [30, 30],

                        iconAnchor:
                            [15, 15]

                    });


                const marker =
                    L.marker(
                        [
                            point.lat,
                            point.lng
                        ],
                        {
                            icon: icon,

                            draggable: true
                        }
                    ).addTo(map);


                /*
                |------------------------------------------------------
                | POPUP
                |------------------------------------------------------
                */

                marker.bindPopup(
                    `
                    <div class="gps-popup">

                        <strong>
                            Titik ${number}
                        </strong>

                        <br>

                        Latitude:
                        ${Number(point.lat).toFixed(7)}

                        <br>

                        Longitude:
                        ${Number(point.lng).toFixed(7)}

                    </div>
                    `
                );


                /*
                |------------------------------------------------------
                | DRAG MARKER
                |------------------------------------------------------
                */

                marker.on(
                    'dragend',
                    function (event) {

                        const position =
                            event.target.getLatLng();


                        points[index] = {

                            lat: Number(
                                position.lat.toFixed(7)
                            ),

                            lng: Number(
                                position.lng.toFixed(7)
                            )

                        };


                        renderPoints();

                        drawPolygon();

                        updateFormData();

                    }
                );


                pointMarkers.push(marker);


                /*
                |------------------------------------------------------
                | LIST ITEM
                |------------------------------------------------------
                */

                const item =
                    document.createElement(
                        'div'
                    );


                item.className =
                    'mb-2 rounded-lg border border-[#e1e8ee] bg-[#fafcfd] p-3';


                item.innerHTML = `

                    <div class="flex items-start justify-between gap-2">

                        <div>

                            <p class="text-xs font-extrabold text-navy-900">
                                Titik ${number}
                            </p>

                            <p class="mt-1 font-mono text-[10px] text-muted">
                                Lat:
                                ${Number(point.lat).toFixed(7)}
                            </p>

                            <p class="font-mono text-[10px] text-muted">
                                Lng:
                                ${Number(point.lng).toFixed(7)}
                            </p>

                        </div>


                        <button
                            type="button"
                            data-delete-point="${index}"
                            class="rounded-md px-2 py-1 text-[10px] font-bold text-outside hover:bg-red-50"
                        >
                            Hapus
                        </button>

                    </div>

                `;


                pointList.appendChild(item);

            }
        );


        /*
        |--------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------
        */

        pointList
            .querySelectorAll(
                '[data-delete-point]'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const index =
                                Number(
                                    button.dataset.deletePoint
                                );


                            points.splice(
                                index,
                                1
                            );


                            renderPoints();

                            drawPolygon();

                            updateFormData();

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------
        | COUNT
        |--------------------------------------------------------------
        */

        pointCount.textContent =
            points.length;


        updateValidity();

    }


    /*
    |--------------------------------------------------------------------------
    | DRAW POLYGON
    |--------------------------------------------------------------------------
    */

    function drawPolygon()
    {

        if (polygon) {

            map.removeLayer(
                polygon
            );

            polygon = null;

        }


        if (points.length < 2) {
            return;
        }


        const coordinates =
            points.map(
                function (point) {

                    return [
                        point.lat,
                        point.lng
                    ];

                }
            );


        polygon =
            L.polygon(
                coordinates,
                {

                    color: '#2563eb',

                    weight: 3,

                    fillColor: '#3b82f6',

                    fillOpacity: 0.15

                }
            ).addTo(map);


        /*
        |--------------------------------------------------------------
        | FIT MAP
        |--------------------------------------------------------------
        */

        if (points.length >= 3) {

            map.fitBounds(
                polygon.getBounds(),
                {
                    padding: [
                        30,
                        30
                    ]
                }
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE HIDDEN INPUT
    |--------------------------------------------------------------------------
    */

    function updateFormData()
    {

        pointsInput.value =
            JSON.stringify(
                points
            );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    function updateValidity()
    {

        const valid =
            points.length >= 3;


        validity.dataset.valid =
            valid
                ? 'true'
                : 'false';


        if (valid) {

            validityText.textContent =
                'Area siap disimpan';

            validityIcon.textContent =
                '✓';

        } else {

            validityText.textContent =
                'Tambahkan minimal 3 titik';

            validityIcon.textContent =
                '!';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function (event) {

            updateFormData();


            if (points.length < 3) {

                event.preventDefault();

                alert(
                    'Tambahkan minimal 3 titik koordinat terlebih dahulu.'
                );

                return;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD DATA LAMA
    |--------------------------------------------------------------------------
    */

    renderPoints();

    drawPolygon();

    updateFormData();


    /*
    |--------------------------------------------------------------------------
    | JIKA SUDAH ADA TITIK
    |--------------------------------------------------------------------------
    */

    if (points.length >= 2) {

        const bounds =
            L.latLngBounds(
                points.map(
                    function (point) {

                        return [
                            point.lat,
                            point.lng
                        ];

                    }
                )
            );


        map.fitBounds(
            bounds,
            {
                padding: [
                    30,
                    30
                ]
            }
        );

    }

});
</script>

@endsection
