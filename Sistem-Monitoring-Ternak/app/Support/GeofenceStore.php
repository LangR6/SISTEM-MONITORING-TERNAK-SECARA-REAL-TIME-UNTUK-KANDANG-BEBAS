<?php

namespace App\Support;

/**
 * Penyimpanan area geofence.
 *
 * Disimpan di session agar bisa dicoba tanpa database. Untuk produksi, ganti
 * get()/save() dengan model Eloquent (mis. tabel `geofences` dengan kolom
 * name dan points JSON); pemanggilnya tidak perlu berubah.
 */
class GeofenceStore
{
    private const SESSION_KEY = 'geofence';

    private const DEFAULT_NAME = 'Kandang Utama';

    /**
     * Simpul poligon bawaan desain (persen x,y pada peta).
     */
    private const DEFAULT_POINTS = [
        [20, 26],
        [61, 17],
        [76, 47],
        [67, 82],
        [29, 78],
        [15, 52],
    ];

    /**
     * Ambil data geofence dari session.
     *
     * @return array{
     *     name: string,
     *     points: array<int, array<string, mixed>>,
     *     area_label: string,
     *     label_anchor: array
     * }
     */
    public function get(): array
    {
        $stored = session(self::SESSION_KEY);

        $name = $stored['name'] ?? self::DEFAULT_NAME;

        $points = $stored['points'] ?? $this->defaultPoints();

        return [
            'name' => $name,
            'points' => $points,
            'area_label' => MapProjection::areaLabel($points),
            'label_anchor' => MapProjection::labelAnchor($points),
        ];
    }

    /**
     * Simpan area geofence berdasarkan koordinat GPS.
     *
     * Koordinat GPS:
     *     latitude  = lat
     *     longitude = lng
     *
     * Kemudian dikonversi menjadi:
     *     x = posisi horizontal pada peta ilustrasi (%)
     *     y = posisi vertikal pada peta ilustrasi (%)
     *
     * @param array<int, array{lat: string|float, lng: string|float}> $points
     */
    public function save(string $name, array $points): void
    {
        $normalized = array_map(function (array $p) {
            $lat = (float) $p['lat'];
            $lng = (float) $p['lng'];

            /*
             * Konversi GPS -> posisi pada peta ilustrasi.
             */
            $xy = MapProjection::toXY($lat, $lng);

            /*
             * Logging sementara untuk pengecekan.
             *
             * Hasilnya bisa dilihat di:
             * storage/logs/laravel.log
             */
            logger()->info('GEOfence GPS Baru', [
                'lat' => $lat,
                'lng' => $lng,
                'x' => $xy['x'],
                'y' => $xy['y'],
            ]);

            return [
                'lat' => number_format($lat, 6, '.', ''),
                'lng' => number_format($lng, 6, '.', ''),
                'x' => $xy['x'],
                'y' => $xy['y'],
            ];
        }, array_values($points));

        /*
         * Simpan hasil ke session.
         */
        session([
            self::SESSION_KEY => [
                'name' => $name,
                'points' => $normalized,
            ],
        ]);

        /*
         * Logging seluruh data yang sudah disimpan.
         */
        logger()->info('GEOfence Berhasil Disimpan', [
            'name' => $name,
            'points' => $normalized,
        ]);
    }

    /**
     * Data geofence default.
     *
     * @return array<int, array{
     *     lat: string,
     *     lng: string,
     *     x: int,
     *     y: int
     * }>
     */
    private function defaultPoints(): array
    {
        return array_map(
            fn (array $p) => [
                ...MapProjection::toLatLng($p[0], $p[1]),
                'x' => $p[0],
                'y' => $p[1],
            ],
            self::DEFAULT_POINTS
        );
    }
}
