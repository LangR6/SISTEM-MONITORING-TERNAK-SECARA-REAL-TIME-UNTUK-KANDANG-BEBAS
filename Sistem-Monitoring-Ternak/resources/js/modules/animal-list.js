/**
 * ============================================================
 * ANIMAL LIST
 * ============================================================
 *
 * Fungsi:
 * - Search ID ternak
 * - Filter jenis
 * - Filter perangkat
 * - Filter lokasi
 * - Pilih ternak dari daftar
 * - Menerima pilihan ternak dari peta
 *
 * Event:
 * - animal:selected
 * ============================================================
 */

export function initAnimalLists() {

    document
        .querySelectorAll('[data-animal-list]')
        .forEach(setupList);

}


/**
 * ============================================================
 * SETUP LIST
 * ============================================================
 */

function setupList(root) {

    const input =
        root.querySelector('[data-list-search]');

    const clear =
        root.querySelector('[data-list-clear]');

    const empty =
        root.querySelector('[data-list-empty]');

    const reset =
        root.querySelector('[data-list-reset]');

    const typeSelect =
        root.querySelector('[data-type-filter]');

    const deviceSelect =
        root.querySelector('[data-device-filter]');

    const locationSelect =
        root.querySelector('[data-location-filter]');


    const rows = [
        ...root.querySelectorAll('[data-animal-row]')
    ];


    const limit =
        Number(root.dataset.limit) || Infinity;


    let type = 'Semua';
    let device = 'Semua';
    let location = 'Semua';


    /**
     * ========================================================
     * PILIH TERNAK
     * ========================================================
     */

    function selectAnimal(row, dispatchEvent = true) {

        if (!row) {
            return;
        }


        const animalId =
            row.dataset.id;


        if (!animalId) {
            return;
        }


        /*
         * Hapus selected dari semua row
         */

        rows.forEach(function (item) {

            item.removeAttribute(
                'data-selected'
            );

        });


        /*
         * Tambahkan selected
         */

        row.setAttribute(
            'data-selected',
            'true'
        );


        /*
         * Scroll ke row yang dipilih
         */

        row.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
        });


        /*
         * Kirim event ke map
         *
         * Hanya dilakukan jika pilihan berasal
         * dari daftar ternak.
         *
         * Kalau berasal dari map, jangan kirim
         * kembali supaya tidak terjadi loop.
         */

        if (dispatchEvent) {

            document.dispatchEvent(

                new CustomEvent(
                    'animal:selected',
                    {
                        detail: {
                            id: animalId,
                        },
                    }
                )

            );

        }

    }


    /**
     * ========================================================
     * KLIK DARI DAFTAR TERNAK
     * ========================================================
     */

    rows.forEach(function (row) {

        if (
            row.tagName.toLowerCase() === 'button'
        ) {

            row.addEventListener(
                'click',
                function () {

                    selectAnimal(
                        row,
                        true
                    );

                }
            );

        }

    });


    /**
     * ========================================================
     * PILIH TERNAK DARI PETA
     * ========================================================
     *
     * Ketika marker peta diklik,
     * map akan mengirim:
     *
     * animal:selected
     *
     * detail:
     * {
     *     id: "SAPI-01"
     * }
     *
     */

    document.addEventListener(
        'animal:selected',
        function (event) {

            const animalId =
                event.detail?.id;


            if (!animalId) {
                return;
            }


            /*
             * Cari row berdasarkan ID
             */

            const row =
                rows.find(function (item) {

                    return String(
                        item.dataset.id
                    ) === String(animalId);

                });


            if (!row) {
                return;
            }


            /*
             * Jangan dispatch event lagi.
             *
             * Karena sumbernya sudah dari map.
             */

            selectAnimal(
                row,
                false
            );

        }
    );


    /**
     * ========================================================
     * FILTER
     * ========================================================
     */

    function apply() {

        const query =
            input?.value
                .trim()
                .toLowerCase() ?? '';


        let shown = 0;
        let matched = 0;


        rows.forEach(function (row) {

            const id =
                row.dataset.id
                    ?.toLowerCase() ?? '';


            const rowType =
                row.dataset.type ?? '';


            const rowDevice =
                row.dataset.device ?? '';


            const rowLocation =
                row.dataset.location ?? '';


            const matchSearch =
                id.includes(query);


            const matchType =
                type === 'Semua' ||
                rowType === type;


            const matchDevice =
                device === 'Semua' ||
                rowDevice === device;


            const matchLocation =
                location === 'Semua' ||
                rowLocation === location;


            const match =
                matchSearch &&
                matchType &&
                matchDevice &&
                matchLocation;


            if (match) {
                matched += 1;
            }


            const visible =
                match &&
                shown < limit;


            row.hidden =
                !visible;


            if (visible) {
                shown += 1;
            }

        });


        if (empty) {

            empty.hidden =
                matched > 0;

        }


        if (clear) {

            clear.hidden =
                query === '';

        }

    }


    /**
     * ========================================================
     * SEARCH
     * ========================================================
     */

    input?.addEventListener(
        'input',
        apply
    );


    /**
     * ========================================================
     * CLEAR SEARCH
     * ========================================================
     */

    clear?.addEventListener(
        'click',
        function () {

            if (input) {

                input.value = '';

                input.focus();

            }

            apply();

        }
    );


    /**
     * ========================================================
     * FILTER JENIS
     * ========================================================
     */

    typeSelect?.addEventListener(
        'change',
        function () {

            type =
                typeSelect.value;

            apply();

        }
    );


    /**
     * ========================================================
     * FILTER PERANGKAT
     * ========================================================
     */

    deviceSelect?.addEventListener(
        'change',
        function () {

            device =
                deviceSelect.value;

            apply();

        }
    );


    /**
     * ========================================================
     * FILTER LOKASI
     * ========================================================
     */

    locationSelect?.addEventListener(
        'change',
        function () {

            location =
                locationSelect.value;

            apply();

        }
    );


    /**
     * ========================================================
     * RESET
     * ========================================================
     */

    reset?.addEventListener(
        'click',
        function () {

            if (input) {
                input.value = '';
            }


            type = 'Semua';

            device = 'Semua';

            location = 'Semua';


            if (typeSelect) {
                typeSelect.value = 'Semua';
            }


            if (deviceSelect) {
                deviceSelect.value = 'Semua';
            }


            if (locationSelect) {
                locationSelect.value = 'Semua';
            }


            apply();

        }
    );


    /**
     * ========================================================
     * INITIAL
     * ========================================================
     */

    apply();

}