<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CalculationRound extends Enum
{
    const ROUND = 0;
    const FLOOR = 1;
    const CEIL = 2;

    /**
     * Apply calculate round tax
     *
     * @param float $tax  Tax
     * @param int   $type Type
     *
     * @return float
     */
    public static function apply(float $tax, int $type): float
    {
        return match ($type) {
            self::FLOOR => floor($tax),
            self::CEIL => ceil($tax),
            self::ROUND => round($tax),
            default => $tax,
        };
    }
}
