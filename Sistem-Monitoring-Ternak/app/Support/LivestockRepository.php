<?php

namespace App\Support;

use App\Enums\LivestockStatus;
use Illuminate\Support\Collection;

/**
 * Sumber data dummy ternak.
 *
 * Posisi dummy dibuat berdasarkan geofence yang tersimpan.
 * Status DALAM/KELUAR ditentukan otomatis menggunakan point-in-polygon.
 *
 * Struktur data:
 * id, type, status, time, lat, lng, x, y
 */
class LivestockRepository
{
    public const LAST_SYNC = '09:15';

    /**
     * Mode posisi dummy:
     * - inside  = harus berada di dalam kandang
     * - outside = harus berada di luar kandang
     * - offline = tracker offline
     */
    private const ROWS = [
        ['SAPI-001', 'Sapi', 'inside',  '09:15'],
        ['SAPI-002', 'Sapi', 'inside',  '09:15'],
        ['SAPI-003', 'Sapi', 'inside',  '09:15'],
        ['SAPI-004', 'Sapi', 'inside',  '09:14'],
        ['SAPI-005', 'Sapi', 'inside',  '09:15'],
        ['SAPI-006', 'Sapi', 'inside',  '09:15'],
        ['SAPI-007', 'Sapi', 'outside', '09:15'],
        ['SAPI-008', 'Sapi', 'offline', '08:45'],

        ['SAPI-009', 'Sapi', 'inside',  '09:15'],
        ['SAPI-010', 'Sapi', 'outside', '09:15'],
        ['SAPI-011', 'Sapi', 'inside',  '09:15'],
        ['SAPI-012', 'Sapi', 'inside',  '09:12'],

        ['KAMBING-001', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-002', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-003', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-004', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-005', 'Kambing', 'outside', '09:15'],
        ['KAMBING-006', 'Kambing', 'inside',  '09:14'],
        ['KAMBING-007', 'Kambing', 'outside', '09:15'],
        ['KAMBING-008', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-009', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-010', 'Kambing', 'inside',  '09:11'],
        ['KAMBING-011', 'Kambing', 'inside',  '09:15'],
        ['KAMBING-012', 'Kambing', 'outside', '09:15'],
    ];

    public function __construct(
        private readonly GeofenceStore $geofence,
    ) {
    }

    /**
     * Ambil seluruh data dummy ternak.
     */
    public function all(): Collection
    {
        $geofence = $this->geofence->get();

        $insidePoints = $this->generateInsidePoints($geofence['points']);
        $outsidePoints = $this->generateOutsidePoints($geofence['points']);

        $insideIndex = 0;
        $outsideIndex = 0;

        return collect(self::ROWS)->map(function (array $row) use (
            &$insideIndex,
            &$outsideIndex,
            $insidePoints,
            $outsidePoints,
            $geofence
        ) {
            [$id, $type, $mode, $time] = $row;

            /*
             * Tentukan posisi berdasarkan mode dummy.
             */
            if ($mode === 'outside') {
                $position = $outsidePoints[$outsideIndex % count($outsidePoints)];
                $outsideIndex++;
            } else {
                $position = $insidePoints[$insideIndex % count($insidePoints)];
                $insideIndex++;
            }

            $lat = $position['lat'];
            $lng = $position['lng'];

            /*
             * Tracker offline tetap OFFLINE.
             *
             * Untuk tracker aktif, status dihitung berdasarkan
             * apakah koordinat berada di dalam geofence.
             */
            if ($mode === 'offline') {
                $status = LivestockStatus::Offline;
            } else {
                $isInside = $this->pointInPolygon(
                    $lat,
                    $lng,
                    $geofence['points']
                );

                $status = $isInside
                    ? LivestockStatus::Inside
                    : LivestockStatus::Outside;
            }

            return [
                'id' => $id,
                'type' => $type,
                'status' => $status,
                'time' => $time,

                /*
                 * x/y tetap disediakan agar kompatibel dengan
                 * bagian lama aplikasi yang mungkin masih membutuhkannya.
                 */
                'x' => $position['x'],
                'y' => $position['y'],

                /*
                 * Leaflet menggunakan koordinat GPS ini.
                 */
                'lat' => $lat,
                'lng' => $lng,
            ];
        })->values();
    }

    /**
     * Cari ternak berdasarkan ID.
     */
    public function find(?string $id): ?array
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Ringkasan dashboard.
     *
     * Jumlah inside/outside/offline otomatis mengikuti
     * hasil pemeriksaan geofence.
     */
    public function summary(): array
    {
        $all = $this->all();

        $count = fn (LivestockStatus $status) =>
            $all->where('status', $status)->count();

        $total = $all->count();

        return [
            'total' => $total,

            'inside' => $count(LivestockStatus::Inside),

            'outside' => $count(LivestockStatus::Outside),

            'offline' => $count(LivestockStatus::Offline),

            'inside_percent' => $total
                ? (int) round(
                    $count(LivestockStatus::Inside) / $total * 100
                )
                : 0,
        ];
    }

    /**
     * Data untuk JavaScript.
     */
    public function forJs(): array
    {
        return $this->all()
            ->mapWithKeys(fn (array $animal) => [
                $animal['id'] => [
                    'id' => $animal['id'],
                    'type' => $animal['type'],
                    'status' => $animal['status']->value,
                    'statusLabel' => $animal['status']->label(),
                    'time' => $animal['time'],
                    'lat' => $animal['lat'],
                    'lng' => $animal['lng'],
                ],
            ])
            ->all();
    }

    /**
     * Membuat beberapa titik yang pasti berada di dalam geofence.
     */
    private function generateInsidePoints(array $points): array
    {
        if (count($points) < 3) {
            return [
                [
                    'lat' => 1.027500,
                    'lng' => 103.945300,
                    'x' => 50,
                    'y' => 50,
                ],
            ];
        }

        $candidates = [];

        /*
         * 1. Titik rata-rata seluruh vertex.
         */
        $centerLat = collect($points)->avg(
            fn ($point) => (float) $point['lat']
        );

        $centerLng = collect($points)->avg(
            fn ($point) => (float) $point['lng']
        );

        $candidates[] = [
            'lat' => $centerLat,
            'lng' => $centerLng,
        ];

        /*
         * 2. Beberapa titik antara pusat dan vertex.
         *
         * Dengan begitu posisi ternak tidak menumpuk
         * tepat di tengah kandang.
         */
        foreach ($points as $point) {
            $lat = (float) $point['lat'];
            $lng = (float) $point['lng'];

            for ($ratio = 0.25; $ratio <= 0.65; $ratio += 0.20) {
                $candidates[] = [
                    'lat' => $centerLat + (($lat - $centerLat) * $ratio),
                    'lng' => $centerLng + (($lng - $centerLng) * $ratio),
                ];
            }
        }

        /*
         * Hanya ambil titik yang benar-benar berada di dalam polygon.
         */
        $valid = [];

        foreach ($candidates as $candidate) {
            if (
                $this->pointInPolygon(
                    $candidate['lat'],
                    $candidate['lng'],
                    $points
                )
            ) {
                $valid[] = $this->makePosition(
                    $candidate['lat'],
                    $candidate['lng']
                );
            }
        }

        /*
         * Fallback apabila polygon sangat tidak biasa.
         */
        if (empty($valid)) {
            $valid[] = $this->makePosition(
                $centerLat,
                $centerLng
            );
        }

        return $valid;
    }

    /**
     * Membuat beberapa titik yang berada di luar geofence.
     */
    private function generateOutsidePoints(array $points): array
    {
        if (count($points) < 3) {
            return [
                [
                    'lat' => 1.028500,
                    'lng' => 103.946500,
                    'x' => 90,
                    'y' => 10,
                ],
            ];
        }

        $latitudes = array_map(
            fn ($point) => (float) $point['lat'],
            $points
        );

        $longitudes = array_map(
            fn ($point) => (float) $point['lng'],
            $points
        );

        $minLat = min($latitudes);
        $maxLat = max($latitudes);
        $minLng = min($longitudes);
        $maxLng = max($longitudes);

        /*
         * Margin cukup besar supaya titik benar-benar
         * terlihat berada di luar area kandang.
         */
        $latRange = max($maxLat - $minLat, 0.0001);
        $lngRange = max($maxLng - $minLng, 0.0001);

        $marginLat = $latRange * 0.35;
        $marginLng = $lngRange * 0.35;

        $candidates = [
            [
                'lat' => $maxLat + $marginLat,
                'lng' => $minLng,
            ],
            [
                'lat' => $maxLat + $marginLat,
                'lng' => $maxLng,
            ],
            [
                'lat' => $minLat - $marginLat,
                'lng' => $minLng,
            ],
            [
                'lat' => $minLat - $marginLat,
                'lng' => $maxLng,
            ],
            [
                'lat' => ($minLat + $maxLat) / 2,
                'lng' => $maxLng + $marginLng,
            ],
            [
                'lat' => ($minLat + $maxLat) / 2,
                'lng' => $minLng - $marginLng,
            ],
        ];

        $valid = [];

        foreach ($candidates as $candidate) {
            if (
                !$this->pointInPolygon(
                    $candidate['lat'],
                    $candidate['lng'],
                    $points
                )
            ) {
                $valid[] = $this->makePosition(
                    $candidate['lat'],
                    $candidate['lng']
                );
            }
        }

        /*
         * Fallback jika semua kandidat somehow masuk polygon.
         */
        if (empty($valid)) {
            $valid[] = $this->makePosition(
                $maxLat + ($latRange * 0.5),
                $maxLng + ($lngRange * 0.5)
            );
        }

        return $valid;
    }

    /**
     * Konversi koordinat GPS menjadi format data aplikasi.
     */
    private function makePosition(float $lat, float $lng): array
    {
        /*
         * x/y hanya kompatibilitas dengan sistem lama.
         * Leaflet menggunakan lat/lng.
         */
        $x = round(
            (($lng - MapProjection::WEST) /
                (MapProjection::EAST - MapProjection::WEST)) * 100,
            2
        );

        $y = round(
            ((MapProjection::NORTH - $lat) /
                (MapProjection::NORTH - MapProjection::SOUTH)) * 100,
            2
        );

        return [
            'lat' => number_format($lat, 6, '.', ''),
            'lng' => number_format($lng, 6, '.', ''),
            'x' => $x,
            'y' => $y,
        ];
    }

    /**
     * Algoritma Point in Polygon.
     *
     * Mengembalikan TRUE jika koordinat berada di dalam
     * area geofence.
     */
    private function pointInPolygon(
        float $lat,
        float $lng,
        array $polygon
    ): bool {
        $inside = false;
        $count = count($polygon);

        if ($count < 3) {
            return false;
        }

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $latI = (float) $polygon[$i]['lat'];
            $lngI = (float) $polygon[$i]['lng'];

            $latJ = (float) $polygon[$j]['lat'];
            $lngJ = (float) $polygon[$j]['lng'];

            /*
             * Cek apakah garis polygon melewati
             * garis horizontal dari titik yang diuji.
             */
            $intersects = (
                (($latI > $lat) !== ($latJ > $lat))
                &&
                (
                    $lng <
                    (
                        ($lngJ - $lngI)
                        * ($lat - $latI)
                        / (($latJ - $latI) ?: 0.000000001)
                        + $lngI
                    )
                )
            );

            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
