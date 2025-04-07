<?php

namespace Unicart\Classes;

final class Discount
{
    const FLAT_TYPE = 'flat';
    const PERCENTAGE_TYPE = 'percentage';

    private function __construct() {}

    public static function flatDiscount(int|float $payable, int|float $discount)
    {
        return round($discount >= $payable ? 0 : $payable - $discount, 2);
    }

    public static function percentageDiscount(int|float $payable, int|float $percentage, int|float $upto)
    {
        $discount = $upto > 0 ? min($upto, (($payable * $percentage) / 100)) : (($payable * $percentage) / 100);
        return self::flatDiscount($payable, $discount);
    }
}
