import {
    centroid,
    clamp,
    createProjection,
    formatHectares,
    labelAnchor,
} from './map-projection';

const MIN_POINTS = 3;
const MAX_POINTS = 50;

const HANDLE_CLASS =
    'absolute z-[6] size-3.5 -translate-x-1/2 -translate-y-1/2 cursor-grab touch-none rounded-full border-2 border-inside bg-white shadow-[0_1px_4px_rgba(21,48,72,0.3)] active:cursor-grabbing';

/**
 * Editor area geofence di halaman Pengaturan Geofence.
 *
 * Fitur:
 * - Klik peta       -> tambah titik
 * - Seret titik     -> pindahkan titik
 * - Tombol sampah   -> hapus titik
 * - Ambil Titik     -> mengambil koordinat GPS perangkat
 *
 * Data yang dikirim ke server:
 * [
 *     {
 *         lat: "...",
 *         lng: "..."
 *     }
 * ]
 */

export function initGeofenceEditor() {
    const form = document.querySelector('[data-geofence-editor]');
    const dataEl = document.getElementById('geofence-data');

    if (!form || !dataEl) return;

    const map = form.querySelector('[data-map]');
    const stage = map?.querySelector('[data-map-stage]');
    const polygon = map?.querySelector('[data-geofence-polygon]');
    const areaLabel = map?.querySelector('[data-area-label]');
    const areaNameEl = map?.querySelector('[data-area-name]');
    const areaSizeEl = map?.querySelector('[data-area-size]');

    const list = form.querySelector('[data-point-list]');
    const countEl = form.querySelector('[data-point-count]');
    const nameInput = form.querySelector('[data-area-name-input]');
    const pointsInput = form.querySelector('[data-points-input]');

    const validity = form.querySelector('[data-validity]');
    const validityText = form.querySelector('[data-validity-text]');
    const validityIcon = form.querySelector('[data-validity-icon]');

    const submitButtons = [
        ...form.querySelectorAll('[data-submit]'),
    ];

    const addCurrentPointButton =
        form.querySelector('[data-add-current-point]');

    const trashIconElement =
        document.querySelector('[data-trash-icon]');

    const trashIcon =
        trashIconElement?.innerHTML ?? '×';

    if (
        !map ||
        !stage ||
        !polygon ||
        !areaLabel ||
        !areaNameEl ||
        !areaSizeEl ||
        !list ||
        !countEl ||
        !nameInput ||
        !pointsInput ||
        !validity ||
        !validityText ||
        !validityIcon
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | PROJECTION
    |--------------------------------------------------------------------------
    */

    const projection =
        createProjection(
            JSON.parse(map.dataset.bounds)
        );


    /*
    |--------------------------------------------------------------------------
    | DATA AWAL
    |--------------------------------------------------------------------------
    */

    /**
     * @type {{
     *   lat: string|number,
     *   lng: string|number,
     *   x: number,
     *   y: number
     * }[]}
     */

    let points =
        JSON.parse(dataEl.textContent).points.map((point) => ({
            lat: point.lat,
            lng: point.lng,
            x: Number(point.x),
            y: Number(point.y),
        }));


    const handles = [];


    /*
    |--------------------------------------------------------------------------
    | BUAT TITIK DARI POSISI X/Y
    |--------------------------------------------------------------------------
    */

    const makePoint = (x, y) => {
        x = clamp(x, 0, 100);
        y = clamp(y, 0, 100);

        const latLng =
            projection.toLatLng(x, y);

        return {
            x,
            y,
            lat: latLng.lat,
            lng: latLng.lng,
        };
    };


    /*
    |--------------------------------------------------------------------------
    | BUAT TITIK DARI GPS LAT/LNG
    |--------------------------------------------------------------------------
    */

    const makePointFromLatLng = (lat, lng) => {
        const position =
            projection.toXY(
                Number(lat),
                Number(lng)
            );

        return {
            lat: Number(lat).toFixed(6),
            lng: Number(lng).toFixed(6),
            x: clamp(position.x, 0, 100),
            y: clamp(position.y, 0, 100),
        };
    };


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    const isValid = () => {
        return (
            points.length >= MIN_POINTS &&
            nameInput.value.trim() !== ''
        );
    };


    /*
    |--------------------------------------------------------------------------
    | RENDER BENTUK GEOFENCE
    |--------------------------------------------------------------------------
    */

    function renderShape() {
        polygon.setAttribute(
            'points',
            points
                .map(
                    (point) =>
                        `${point.x.toFixed(2)},${point.y.toFixed(2)}`
                )
                .join(' ')
        );

        const anchor =
            labelAnchor(points);

        areaLabel.style.left =
            `${anchor.x}%`;

        areaLabel.style.top =
            `${anchor.y}%`;

        areaLabel.hidden =
            points.length < MIN_POINTS;

        areaSizeEl.textContent =
            formatHectares(
                projection.areaHectares(points)
            );

        areaNameEl.textContent =
            nameInput.value.trim() ||
            'Area tanpa nama';

        /*
        |--------------------------------------------------------------
        | DATA YANG DIKIRIM KE LARAVEL
        |--------------------------------------------------------------
        */

        pointsInput.value =
            JSON.stringify(
                points.map(
                    ({ lat, lng }) => ({
                        lat,
                        lng,
                    })
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER HANDLE / TITIK
    |--------------------------------------------------------------------------
    */

    function renderHandles({ rebuild }) {
        if (rebuild) {
            handles
                .splice(0)
                .forEach(
                    (element) =>
                        element.remove()
                );

            points.forEach((_, index) => {
                const element =
                    document.createElement('div');

                element.className =
                    HANDLE_CLASS;

                element.dataset.handle =
                    String(index);

                element.title =
                    `Titik ${index + 1}`;

                stage.appendChild(element);

                handles.push(element);
            });
        }

        points.forEach((point, index) => {
            if (!handles[index]) return;

            handles[index].style.left =
                `${point.x}%`;

            handles[index].style.top =
                `${point.y}%`;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER DAFTAR TITIK
    |--------------------------------------------------------------------------
    */

    function renderList() {
        countEl.textContent =
            String(points.length);

        list.innerHTML =
            points
                .map(
                    (point, index) => `
                        <div
                            class="grid grid-cols-[27px_1fr_27px] items-center gap-[9px] border-b border-line px-[7px] py-[11px]"
                        >
                            <span
                                class="grid size-[25px] place-items-center rounded-full bg-inside text-[10px] font-extrabold text-white"
                            >
                                ${index + 1}
                            </span>

                            <div class="flex flex-col">
                                <strong class="text-xs">
                                    Titik ${index + 1}
                                </strong>

                                <small class="mt-[3px] text-[10px] text-muted">
                                    Lat: ${point.lat}
                                </small>

                                <small class="mt-[3px] text-[10px] text-muted">
                                    Lng: ${point.lng}
                                </small>
                            </div>

                            <button
                                type="button"
                                data-remove="${index}"
                                aria-label="Hapus titik ${index + 1}"
                                class="grid size-[26px] place-items-center rounded-md bg-transparent p-0 text-[#9ba8b2] hover:bg-outside-soft hover:text-outside"
                            >
                                ${trashIcon}
                            </button>
                        </div>
                    `
                )
                .join('');
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER STATUS
    |--------------------------------------------------------------------------
    */

    function renderStatus() {
        const valid =
            isValid();

        const enough =
            points.length >= MIN_POINTS;

        validity.dataset.valid =
            String(valid);

        validityIcon.textContent =
            valid ? '✓' : '!';

        validityText.textContent =
            valid
                ? 'Area siap disimpan'
                : enough
                    ? 'Isi nama area'
                    : 'Tambahkan minimum 3 titik';

        submitButtons.forEach(
            (button) => {
                button.disabled =
                    !valid;
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER SEMUA
    |--------------------------------------------------------------------------
    */

    function render({
        rebuildHandles = true,
    } = {}) {
        renderShape();

        renderHandles({
            rebuild: rebuildHandles,
        });

        renderList();

        renderStatus();
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH TITIK
    |--------------------------------------------------------------------------
    */

    function addPoint(point) {
        if (
            points.length >= MAX_POINTS
        ) {
            showMessage(
                `Maksimal ${MAX_POINTS} titik diperbolehkan.`,
                'warning'
            );

            return;
        }

        points.push(point);

        render();
    }


    /*
    |--------------------------------------------------------------------------
    | PESAN
    |--------------------------------------------------------------------------
    */

    function showMessage(
        message,
        type = 'info'
    ) {
        /*
        |--------------------------------------------------------------
        | Coba gunakan sistem flash yang sudah ada.
        |--------------------------------------------------------------
        */

        const existingFlash =
            document.querySelector(
                '[data-geofence-message]'
            );

        if (existingFlash) {
            existingFlash.textContent =
                message;

            existingFlash.dataset.type =
                type;

            existingFlash.hidden =
                false;

            clearTimeout(
                existingFlash._hideTimer
            );

            existingFlash._hideTimer =
                setTimeout(() => {
                    existingFlash.hidden =
                        true;
                }, 5000);

            return;
        }


        /*
        |--------------------------------------------------------------
        | Fallback sederhana
        |--------------------------------------------------------------
        */

        window.alert(message);
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL GPS PERANGKAT
    |--------------------------------------------------------------------------
    */

    function getCurrentLocation() {
        if (
            !navigator.geolocation
        ) {
            showMessage(
                'Browser atau perangkat ini tidak mendukung GPS.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------
        | Ubah tampilan tombol
        |--------------------------------------------------------------
        */

        if (addCurrentPointButton) {
            addCurrentPointButton.disabled =
                true;

            addCurrentPointButton.dataset.loading =
                'true';

            addCurrentPointButton.setAttribute(
                'aria-busy',
                'true'
            );
        }


        showMessage(
            'Sedang mengambil lokasi GPS. Pastikan izin lokasi sudah diberikan.',
            'info'
        );


        navigator.geolocation.getCurrentPosition(
            (position) => {

                const latitude =
                    position.coords.latitude;

                const longitude =
                    position.coords.longitude;

                const accuracy =
                    position.coords.accuracy;


                /*
                |------------------------------------------------------
                | Buat titik dari GPS asli
                |------------------------------------------------------
                */

                const point =
                    makePointFromLatLng(
                        latitude,
                        longitude
                    );


                /*
                |------------------------------------------------------
                | Cek apakah posisi masih berada
                | dalam batas peta ilustrasi
                |------------------------------------------------------
                */

                const rawPosition =
                    projection.toXY(
                        latitude,
                        longitude
                    );


                const isInsideMapBounds =
                    rawPosition.x >= 0 &&
                    rawPosition.x <= 100 &&
                    rawPosition.y >= 0 &&
                    rawPosition.y <= 100;


                if (!isInsideMapBounds) {
                    showMessage(
                        'Lokasi GPS berada di luar area peta yang tersedia. Pastikan lokasi perangkat berada di sekitar area kandang.',
                        'error'
                    );

                    resetGpsButton();

                    return;
                }


                /*
                |------------------------------------------------------
                | Akurasi GPS
                |------------------------------------------------------
                */

                if (accuracy > 50) {
                    const continueAnyway =
                        window.confirm(
                            `Akurasi GPS saat ini sekitar ${Math.round(accuracy)} meter.\n\nSebaiknya tunggu hingga akurasi lebih baik sebelum menyimpan titik.\n\nTetap gunakan titik ini?`
                        );

                    if (!continueAnyway) {
                        resetGpsButton();

                        return;
                    }
                }


                /*
                |------------------------------------------------------
                | Tambahkan titik
                |------------------------------------------------------
                */

                addPoint(point);


                /*
                |------------------------------------------------------
                | Pesan berhasil
                |------------------------------------------------------
                */

                showMessage(
                    `Titik GPS berhasil ditambahkan. Akurasi ±${Math.round(accuracy)} meter.`,
                    'success'
                );


                resetGpsButton();
            },

            (error) => {

                let message =
                    'Gagal mengambil lokasi GPS.';


                switch (error.code) {

                    case error.PERMISSION_DENIED:
                        message =
                            'Izin lokasi ditolak. Aktifkan izin lokasi untuk browser ini lalu coba lagi.';
                        break;

                    case error.POSITION_UNAVAILABLE:
                        message =
                            'Lokasi GPS tidak tersedia. Pastikan GPS/lokasi perangkat aktif.';
                        break;

                    case error.TIMEOUT:
                        message =
                            'Waktu pengambilan GPS habis. Coba lagi di tempat dengan sinyal GPS lebih baik.';
                        break;

                    default:
                        message =
                            'Terjadi kesalahan saat mengambil lokasi GPS.';
                        break;
                }


                showMessage(
                    message,
                    'error'
                );


                resetGpsButton();
            },

            {
                enableHighAccuracy: true,

                timeout: 15000,

                maximumAge: 0,
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESET TOMBOL GPS
    |--------------------------------------------------------------------------
    */

    function resetGpsButton() {
        if (!addCurrentPointButton) {
            return;
        }

        addCurrentPointButton.disabled =
            false;

        delete addCurrentPointButton.dataset.loading;

        addCurrentPointButton.removeAttribute(
            'aria-busy'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KLIK PETA
    |--------------------------------------------------------------------------
    */

    map.addEventListener(
        'click',
        (event) => {

            if (
                event.target.closest(
                    '[data-handle], [data-map-action]'
                )
            ) {
                return;
            }

            const rect =
                stage.getBoundingClientRect();

            const x =
                (
                    (event.clientX - rect.left) /
                    rect.width
                ) * 100;

            const y =
                (
                    (event.clientY - rect.top) /
                    rect.height
                ) * 100;

            addPoint(
                makePoint(x, y)
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | DRAG TITIK
    |--------------------------------------------------------------------------
    */

    stage.addEventListener(
        'pointerdown',
        (event) => {

            const handle =
                event.target.closest(
                    '[data-handle]'
                );

            if (!handle) {
                return;
            }

            event.preventDefault();

            handle.setPointerCapture(
                event.pointerId
            );

            const index =
                Number(
                    handle.dataset.handle
                );


            const move = (e) => {

                const rect =
                    stage.getBoundingClientRect();

                points[index] =
                    makePoint(
                        (
                            (e.clientX - rect.left) /
                            rect.width
                        ) * 100,

                        (
                            (e.clientY - rect.top) /
                            rect.height
                        ) * 100
                    );


                render({
                    rebuildHandles: false,
                });
            };


            const stop = () => {

                handle.removeEventListener(
                    'pointermove',
                    move
                );

                handle.removeEventListener(
                    'pointerup',
                    stop
                );

                handle.removeEventListener(
                    'pointercancel',
                    stop
                );
            };


            handle.addEventListener(
                'pointermove',
                move
            );

            handle.addEventListener(
                'pointerup',
                stop
            );

            handle.addEventListener(
                'pointercancel',
                stop
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | HAPUS TITIK
    |--------------------------------------------------------------------------
    */

    list.addEventListener(
        'click',
        (event) => {

            const button =
                event.target.closest(
                    '[data-remove]'
                );

            if (!button) {
                return;
            }

            const index =
                Number(
                    button.dataset.remove
                );

            points.splice(
                index,
                1
            );

            render();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | TOMBOL AMBIL TITIK GPS
    |--------------------------------------------------------------------------
    */

    if (addCurrentPointButton) {

        addCurrentPointButton.addEventListener(
            'click',
            () => {
                getCurrentLocation();
            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | NAMA AREA
    |--------------------------------------------------------------------------
    */

    nameInput.addEventListener(
        'input',
        () => {

            renderShape();

            renderStatus();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        (event) => {

            if (!isValid()) {

                event.preventDefault();

                showMessage(
                    points.length < MIN_POINTS
                        ? 'Tambahkan minimum 3 titik koordinat.'
                        : 'Isi nama area terlebih dahulu.',
                    'warning'
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL RENDER
    |--------------------------------------------------------------------------
    */

    render();
}
