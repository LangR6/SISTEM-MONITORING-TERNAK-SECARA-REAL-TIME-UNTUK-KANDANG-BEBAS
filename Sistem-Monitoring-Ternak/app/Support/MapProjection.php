<?php

namespace App\Support;

/**
 * Konversi antara koordinat GPS (lat/lng) dan posisi persen (x/y) pada peta.
 *
 * Peta pada desain adalah kanvas ilustrasi, bukan tile peta sungguhan. Batas
 * di bawah ini menentukan area GPS yang direpresentasikan kanvas 100% x 100%.
 * Nilai yang sama dikirim ke JavaScript (lihat components/map.blade.php)
 * sehingga perhitungan di server dan browser selalu konsisten.
 *
 * Saat memakai peta sungguhan (Leaflet/Google Maps), cukup ganti kelas ini
 * dan modul resources/js/modules/map-projection.js.
 */
class MapProjection
{
    public const WEST = 104.1215;
    public const EAST = 104.1305;
    public const NORTH = 1.1252;
    public const SOUTH = 1.12145;

    public static function bounds(): array
    {
        return [
            'west' => self::WEST,
            'east' => self::EAST,
            'north' => self::NORTH,
            'south' => self::SOUTH,
        ];
    }

    /** @return array{lat: string, lng: string} */
    public static function toLatLng(float $x, float $y): array
    {
        return [
            'lat' => number_format(self::NORTH - ($y / 100) * (self::NORTH - self::SOUTH), 6, '.', ''),
            'lng' => number_format(self::WEST + ($x / 100) * (self::EAST - self::WEST), 6, '.', ''),
        ];
    }

    /** @return array{x: float, y: float} */
    public static function toXY(float $lat, float $lng): array
    {
        return [
            'x' => round((($lng - self::WEST) / (self::EAST - self::WEST)) * 100, 2),
            'y' => round(((self::NORTH - $lat) / (self::NORTH - self::SOUTH)) * 100, 2),
        ];
    }

    /**
     * Luas poligon dalam hektar (rumus shoelace pada koordinat meter).
     *
     * @param  array<int, array{x: float|int, y: float|int}>  $points
     */
    public static function areaHectares(array $points): float
    {
        $count = count($points);
        if ($count < 3) {
            return 0.0;
        }

        $metersPerDegree = 111320;
        $width = (self::EAST - self::WEST) * $metersPerDegree;
        $height = (self::NORTH - self::SOUTH) * $metersPerDegree;

        $sum = 0.0;
        for ($i = 0; $i < $count; $i++) {
            $a = $points[$i];
            $b = $points[($i + 1) % $count];
            $sum += ($a['x'] / 100 * $width) * ($b['y'] / 100 * $height)
                - ($b['x'] / 100 * $width) * ($a['y'] / 100 * $height);
        }

        return abs($sum) / 2 / 10000;
    }

    /** Format Indonesia, contoh: "12,4 ha". */
    public static function areaLabel(array $points): string
    {
        return number_format(self::areaHectares($points), 1, ',', '.').' ha';
    }

    /**
     * Posisi label nama/luas area: di tengah horizontal, tepat di atas tepi
     * teratas poligon, supaya tidak tertutup marker ternak di dalam area.
     */
    public static function labelAnchor(array $points): array
    {
        $count = max(count($points), 1);

        return [
            'x' => round(array_sum(array_column($points, 'x')) / $count, 2),
            'y' => round(max(($points ? min(array_column($points, 'y')) : 50) - 7, 6), 2),
        ];
    }
}
