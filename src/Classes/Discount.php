<?php

namespace Unicart\Classes;

final class Discount
{
    /**
     * Flat discount type identifier.
     */
    const FLAT_TYPE = 'flat';

    /**
     * Percentage discount type identifier.
     */
    const PERCENTAGE_TYPE = 'percentage';

    /**
     * BxGy type identifier.
     */
    const BXGY_TYPE = 'bxgy';

    /**
     * SxGy type identifier.
     */
    const SXGY_TYPE = 'sxgy';

    /**
     * Private constructor to prevent instantiation.
     * This class is intended to be used in a static context only.
     */
    private function __construct() {}

    /**
     * Applies a flat discount to a payable amount.
     *
     * @param int|float $payable The original payable amount before discount.
     * @param int|float $discount The flat discount amount to apply.
     *
     * @return int|float 
     */
    public static function flatDiscount(int|float $payable, int|float $discount): int|float
    {
        return round($discount >= $payable ? 0 : $payable - $discount, 2);
    }

    /**
     * Applies a percentage-based discount to a payable amount, with an optional cap.
     *
     * @param int|float $payable The original payable amount before discount.
     * @param int|float $percentage The discount percentage to apply.
     * @param int|float $upto The maximum discount allowed. Defaults to 0 (no limit).
     *
     * @return int|float 
     */
    public static function percentageDiscount(int|float $payable, int|float $percentage, int|float $upto = 0): int|float
    {
        $discount = $upto > 0 ? min($upto, (($payable * $percentage) / 100)) : (($payable * $percentage) / 100);
        return self::flatDiscount($payable, $discount);
    }

    /**
     * Applies a bxgy-based discount on item with multiple quantity.
     * 
     * @param int|float $price The price of the item.
     * @param int $quantity The item's current quantity.
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * 
     * @return int|float
     */
    public static function bxgy(int|float $price, int $quantity, int $xQuantity, int $yQuantity): int|float
    {
        $sets = intdiv($quantity, $xQuantity + $yQuantity);
        $freeItems = $sets * $yQuantity;
        $paidItems = $quantity - $freeItems;

        return $paidItems * $price;
    }
}
