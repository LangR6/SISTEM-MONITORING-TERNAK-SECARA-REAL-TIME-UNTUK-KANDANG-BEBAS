@props([
    'animals' => [],
    'geofence' => [],
    'editable' => false,
    'readOnly' => false,
    'selected' => null,
    'height' => 'h-[430px]',
])

@php
    $geofencePoints = collect($geofence['points'] ?? [])
        ->filter(fn ($point) => isset($point['lat'], $point['lng']))
        ->map(fn ($point) => [
            'lat' => (float) $point['lat'],
            'lng' => (float) $point['lng'],
        ])
        ->values()
        ->all();

    $mapAnimals = collect($animals)
        ->filter(fn ($animal) => isset($animal['lat'], $animal['lng']))
        ->map(function ($animal) {
            $status = $animal['status'] ?? null;

            if ($status instanceof \BackedEnum) {
                $statusValue = (string) $status->value;
            } elseif ($status instanceof \UnitEnum) {
                $statusValue = $status->name;
            } else {
                $statusValue = (string) $status;
            }

            $statusValue = strtolower(trim($statusValue));

            if (in_array($statusValue, ['inside', 'dalam_kandang', 'dalam kandang'])) {
                $statusValue = 'inside';
            } elseif (in_array($statusValue, ['outside', 'keluar_kandang', 'keluar kandang'])) {
                $statusValue = 'outside';
            } elseif (in_array($statusValue, ['offline', 'mati'])) {
                $statusValue = 'offline';
            }

            $statusLabel = match ($statusValue) {
                'inside' => 'Dalam Kandang',
                'outside' => 'Keluar Kandang',
                'offline' => 'Offline',
                default => 'Tidak Diketahui',
            };

            if (
                $status instanceof \BackedEnum &&
                method_exists($status, 'label')
            ) {
                $statusLabel = $status->label();
            }

            return [
                'id' => $animal['id'] ?? '-',
                'type' => $animal['type'] ?? 'Sapi',
                'status' => $statusValue,
                'statusLabel' => $statusLabel,
                'time' => $animal['time'] ?? '-',
                'lat' => (float) $animal['lat'],
                'lng' => (float) $animal['lng'],
            ];
        })
        ->values()
        ->all();

    if (count($geofencePoints) > 0) {
        $centerLat = collect($geofencePoints)->avg('lat');
        $centerLng = collect($geofencePoints)->avg('lng');
    } elseif (count($mapAnimals) > 0) {
        $centerLat = collect($mapAnimals)->avg('lat');
        $centerLng = collect($mapAnimals)->avg('lng');
    } else {
        $centerLat = 1.0275;
        $centerLng = 103.9453;
    }

    $cattleIcon = config('icons.cattle', '');
    $goatIcon = config('icons.goat', '');
@endphp

<div
    data-map
    class="relative isolate w-full overflow-hidden rounded-[14px] {{ $height }}"
    style="height: 430px; min-height: 360px; position: relative; isolation: isolate;"
>
    <div
        data-leaflet-map
        class="absolute inset-0"
        style="position:absolute; inset:0; width:100%; height:100%; z-index:0;"
    ></div>

    <div
        data-map-controls
        class="absolute right-3 top-3 flex flex-col overflow-hidden rounded-lg border border-[#d9e1e5] bg-white shadow-md"
        style="position:absolute; top:12px; right:12px; z-index:500;"
    >
        <button type="button" data-map-action="zoom-in"
            aria-label="Perbesar peta"
            class="grid size-9 place-items-center border-b border-[#e5eaee] bg-white text-[#476175] hover:bg-[#f7f9fb]">
            <x-icon name="plus" :size="17" />
        </button>

        <button type="button" data-map-action="zoom-out"
            aria-label="Perkecil peta"
            class="grid size-9 place-items-center border-b border-[#e5eaee] bg-white text-[#476175] hover:bg-[#f7f9fb]">
            <x-icon name="minus" :size="17" />
        </button>

        <button type="button" data-map-action="reset"
            aria-label="Kembalikan tampilan"
            class="grid size-9 place-items-center border-b border-[#e5eaee] bg-white text-[#476175] hover:bg-[#f7f9fb]">
            <x-icon name="crosshair" :size="17" />
        </button>

        <button type="button" data-map-action="fullscreen"
            aria-label="Layar penuh"
            class="grid size-9 place-items-center bg-white text-[#476175] hover:bg-[#f7f9fb]">
            <x-icon name="expand" :size="16" />
        </button>
    </div>

    <div
        data-map-legend
        class="absolute bottom-0 left-0 right-0 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-[#e4e9ed] bg-white px-3 py-2 text-[10px] text-[#526879]"
        style="position:absolute; left:0; right:0; bottom:0; z-index:500;"
    >
        <span class="flex items-center gap-1.5">
            <span class="size-2 rounded-full bg-[#1baa68]"></span>
            Dalam Kandang
        </span>
        <span class="flex items-center gap-1.5">
            <span class="size-2 rounded-full bg-[#e05252]"></span>
            Keluar Kandang
        </span>
        <span class="flex items-center gap-1.5">
            <span class="size-2 rounded-full bg-[#8b9aa8]"></span>
            Offline
        </span>
        <span class="flex items-center gap-1.5">
            <span class="h-[7px] w-4 rounded-full border border-[#16a866] bg-[#dff5e9]"></span>
            Area Geofence
        </span>
    </div>
</div>

@once
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        [data-map] {
            display: block !important;
            position: relative !important;
            isolation: isolate !important;
            width: 100% !important;
            height: 430px !important;
            min-height: 360px !important;
            overflow: hidden !important;
            border-radius: 14px;
        }

        [data-map] [data-leaflet-map] {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            z-index: 0 !important;
        }

        [data-map] .leaflet-container {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            font-family: inherit;
            background: #e9eef2;
        }

        [data-map] .leaflet-control-zoom {
            display: none !important;
        }

        [data-map] .leaflet-control-attribution {
            font-size: 8px !important;
            background: rgba(255,255,255,.88) !important;
        }

        [data-map] .leaflet-map-pane {
            z-index: 1 !important;
        }

        [data-map] .leaflet-tile-pane {
            z-index: 2 !important;
        }

        [data-map] .leaflet-overlay-pane {
            z-index: 4 !important;
        }

        [data-map] .leaflet-shadow-pane {
            z-index: 5 !important;
        }

        [data-map] .leaflet-marker-pane {
            z-index: 6 !important;
        }

        [data-map] .leaflet-tooltip-pane {
            z-index: 7 !important;
        }

        [data-map] .leaflet-popup-pane {
            z-index: 8 !important;
        }

        .livestock-marker {
            position: relative;
            display: flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .2s ease;
        }

        .livestock-marker-inner {
            display: flex;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 2px 7px rgba(21,48,72,.25);
        }

        .livestock-marker-inner svg {
            width: 19px;
            height: 19px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .livestock-marker-inside {
            background: #1baa68;
            color: #fff;
        }

        .livestock-marker-outside {
            background: #e05252;
            color: #fff;
        }

        .livestock-marker-offline {
            background: #8b9aa8;
            color: #fff;
        }

        .livestock-marker.selected {
            transform: scale(1.15);
        }

        .livestock-marker.selected .livestock-marker-inner {
            box-shadow:
                0 0 0 3px #fff,
                0 0 0 6px rgba(242,123,32,.75);
        }

        .livestock-marker-label {
            position: absolute;
            top: 38px;
            left: 50%;
            transform: translateX(-50%);
            padding: 2px 5px;
            border-radius: 4px;
            background: rgba(255,255,255,.94);
            color: #17334d;
            font-size: 9px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 1px 4px rgba(21,48,72,.14);
            pointer-events: none;
        }

        .livestock-marker.selected .livestock-marker-label {
            background: #f47b20;
            color: #fff;
        }

        .livestock-popup {
            min-width: 180px;
            font-family: inherit;
        }

        .livestock-popup-title {
            margin-bottom: 4px;
            color: #17334d;
            font-size: 13px;
            font-weight: 800;
        }

        .livestock-popup-type {
            margin-bottom: 8px;
            color: #6b7c8b;
            font-size: 11px;
        }

        .livestock-popup-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .livestock-popup-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .livestock-popup-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-top: 4px;
            margin-top: 4px;
            border-top: 1px solid #edf1f3;
            font-size: 10px;
        }

        .livestock-popup-row span:first-child {
            color: #83929e;
        }

        .livestock-popup-row span:last-child {
            color: #29475d;
            font-weight: 700;
        }

        @media (max-width: 640px) {
            [data-map] {
                height: 360px !important;
                min-height: 360px !important;
                width: 100% !important;
            }

            [data-map] [data-map-controls] {
                top: 8px !important;
                right: 8px !important;
            }

            [data-map] [data-map-legend] {
                gap: 6px 12px;
                padding: 8px 10px;
                font-size: 9px;
            }

            [data-map] .leaflet-control-attribution {
                font-size: 7px !important;
            }
        }
    </style>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endonce

<script>
(function () {
    function initLivestockMaps() {
        if (typeof L === 'undefined') {
            return;
        }

        document.querySelectorAll('[data-map]').forEach(function (container) {
            if (container.dataset.leafletInitialized === 'true') {
                return;
            }

            const mapElement = container.querySelector('[data-leaflet-map]');

            if (!mapElement) {
                return;
            }

            container.dataset.leafletInitialized = 'true';

            const animals = @json($mapAnimals);
            const geofence = @json($geofencePoints);
            const cattleIcon = @json($cattleIcon);
            const goatIcon = @json($goatIcon);
            const selectedAnimalId = @json($selected);

            const center = [
                Number(@json($centerLat)),
                Number(@json($centerLng))
            ];

            const map = L.map(mapElement, {
                zoomControl: false,
                attributionControl: true,
            });

            const defaultZoom = 17;

            map.setView(center, defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);

            let geofenceLayer = null;

            if (geofence.length >= 3) {
                const polygonPoints = geofence.map(function (point) {
                    return [Number(point.lat), Number(point.lng)];
                });

                geofenceLayer = L.polygon(polygonPoints, {
                    color: '#18a968',
                    weight: 2,
                    opacity: 1,
                    fillColor: '#1baa68',
                    fillOpacity: 0.16,
                }).addTo(map);

                geofenceLayer.bindTooltip(
                    @json($geofence['name'] ?? 'Area Geofence'),
                    {
                        sticky: true,
                        direction: 'center',
                        className: 'geofence-tooltip',
                    }
                );

                map.fitBounds(geofenceLayer.getBounds(), {
                    paddingTopLeft: [30, 30],
                    paddingBottomRight: [30, 55],
                    maxZoom: 18,
                });
            }

            function getMarkerClass(status) {
                if (status === 'outside') return 'livestock-marker-outside';
                if (status === 'offline') return 'livestock-marker-offline';
                return 'livestock-marker-inside';
            }

            function getStatusColor(status) {
                if (status === 'outside') return '#e05252';
                if (status === 'offline') return '#8b9aa8';
                return '#1baa68';
            }

            const animalMarkers = {};

            function clearSelectedMarkers() {
                Object.values(animalMarkers).forEach(function (marker) {
                    const element = marker.getElement();
                    if (!element) return;

                    const icon = element.querySelector('.livestock-marker');
                    if (icon) icon.classList.remove('selected');
                });
            }

            function selectMarker(marker) {
                if (!marker) return;

                clearSelectedMarkers();

                const element = marker.getElement();
                if (!element) return;

                const icon = element.querySelector('.livestock-marker');
                if (icon) icon.classList.add('selected');
            }

            function focusAnimal(animalId, openPopup = true) {
                const id = String(animalId ?? '').trim();
                if (!id) return;

                const animal = animals.find(function (item) {
                    return String(item.id) === id;
                });

                const marker = animalMarkers[id];

                if (!animal || !marker) {
                    console.warn('Marker ternak tidak ditemukan:', id);
                    return;
                }

                selectMarker(marker);

                const lat = Number(animal.lat);
                const lng = Number(animal.lng);

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    return;
                }

                map.flyTo([lat, lng], 18, {
                    animate: true,
                    duration: 0.8,
                });

                if (openPopup) {
                    setTimeout(function () {
                        marker.openPopup();
                    }, 850);
                }
            }

            animals.forEach(function (animal) {
                const iconPath = animal.type === 'Kambing'
                    ? goatIcon
                    : cattleIcon;

                const markerClass = getMarkerClass(animal.status);

                const markerHtml = `
                    <div class="livestock-marker">
                        <div class="livestock-marker-inner ${markerClass}">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                ${iconPath}
                            </svg>
                        </div>
                        <span class="livestock-marker-label">${animal.id}</span>
                    </div>
                `;

                const customIcon = L.divIcon({
                    className: '',
                    html: markerHtml,
                    iconSize: [42, 42],
                    iconAnchor: [21, 21],
                    popupAnchor: [0, -18],
                });

                const marker = L.marker(
                    [Number(animal.lat), Number(animal.lng)],
                    {
                        icon: customIcon,
                        title: animal.id,
                    }
                ).addTo(map);

                animalMarkers[String(animal.id)] = marker;

                const statusColor = getStatusColor(animal.status);

                const popupHtml = `
                    <div class="livestock-popup">
                        <div class="livestock-popup-title">${animal.id}</div>
                        <div class="livestock-popup-type">${animal.type}</div>

                        <div class="livestock-popup-status" style="color:${statusColor}">
                            <span class="livestock-popup-status-dot"
                                style="background:${statusColor}"></span>
                            ${animal.statusLabel}
                        </div>

                        <div class="livestock-popup-row">
                            <span>Latitude</span>
                            <span>${Number(animal.lat).toFixed(6)}</span>
                        </div>

                        <div class="livestock-popup-row">
                            <span>Longitude</span>
                            <span>${Number(animal.lng).toFixed(6)}</span>
                        </div>

                        <div class="livestock-popup-row">
                            <span>Update</span>
                            <span>${animal.time} WIB</span>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupHtml);

                marker.on('click', function () {
                    selectMarker(marker);

                    document.dispatchEvent(new CustomEvent('animal:selected', {
                        detail: { id: animal.id },
                    }));
                });
            });

            document.addEventListener('animal:selected', function (event) {
                const animalId = event.detail?.id;
                if (animalId) focusAnimal(animalId, true);
            });

            if (
                selectedAnimalId !== null &&
                selectedAnimalId !== undefined &&
                selectedAnimalId !== ''
            ) {
                setTimeout(function () {
                    focusAnimal(selectedAnimalId, true);
                }, 300);
            }

            const zoomIn = container.querySelector('[data-map-action="zoom-in"]');
            const zoomOut = container.querySelector('[data-map-action="zoom-out"]');
            const reset = container.querySelector('[data-map-action="reset"]');
            const fullscreen = container.querySelector('[data-map-action="fullscreen"]');

            if (zoomIn) zoomIn.addEventListener('click', () => map.zoomIn());
            if (zoomOut) zoomOut.addEventListener('click', () => map.zoomOut());

            if (reset) {
                reset.addEventListener('click', function () {
                    clearSelectedMarkers();

                    if (geofenceLayer) {
                        map.fitBounds(geofenceLayer.getBounds(), {
                            paddingTopLeft: [30, 30],
                            paddingBottomRight: [30, 55],
                            maxZoom: 18,
                        });
                    } else {
                        map.setView(center, defaultZoom);
                    }
                });
            }

            if (fullscreen) {
                fullscreen.addEventListener('click', async function () {
                    try {
                        if (!document.fullscreenElement) {
                            await container.requestFullscreen();
                        } else {
                            await document.exitFullscreen();
                        }
                    } catch (error) {
                        console.warn('Mode layar penuh tidak tersedia:', error);
                    }

                    setTimeout(function () {
                        map.invalidateSize(true);
                    }, 250);
                });
            }

            /*
             * Perbaikan ukuran peta:
             * - Setelah halaman selesai dirender
             * - Saat ukuran container berubah
             * - Saat layar HP berotasi
             * - Saat mode fullscreen berubah
             */
            function refreshMapSize() {
                requestAnimationFrame(function () {
                    map.invalidateSize(true);
                });
            }

            setTimeout(refreshMapSize, 100);
            setTimeout(refreshMapSize, 400);
            setTimeout(refreshMapSize, 900);

            if (typeof ResizeObserver !== 'undefined') {
                const resizeObserver = new ResizeObserver(function () {
                    refreshMapSize();
                });

                resizeObserver.observe(container);
            }

            window.addEventListener('resize', refreshMapSize);
            window.addEventListener('orientationchange', function () {
                setTimeout(refreshMapSize, 300);
            });

            document.addEventListener('fullscreenchange', function () {
                setTimeout(refreshMapSize, 200);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLivestockMaps);
    } else {
        initLivestockMaps();
    }
})();
</script>
