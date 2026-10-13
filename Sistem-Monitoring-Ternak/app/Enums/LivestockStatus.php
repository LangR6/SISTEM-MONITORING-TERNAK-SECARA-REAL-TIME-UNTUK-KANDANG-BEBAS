<?php

namespace App\Enums;

enum LivestockStatus: string
{
    case Inside = 'inside';
    case Outside = 'outside';
    case Offline = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::Inside => 'Dalam Kandang',
            self::Outside => 'Keluar Kandang',
            self::Offline => 'Offline',
        };
    }
}
