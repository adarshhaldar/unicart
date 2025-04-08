<?php

namespace Unicart\Classes;

use Unicart\Checks\ItemCheck;
use Unicart\Formats\OutputFormat;

class Item
{
    use ItemCheck, OutputFormat;

    /**
     * Item unique identifier
     * @var int|string
     */
    private $id = null;

    /**
     * Single item price
     * @var int|float
     */
    private $price = null;

    /**
     * Item quantity
     * @var int
     */
    private $quantity = null;

    /**
     * Initial payable (before discount, delivery charge, or tax application)
     * @var int|float
     */
    private $originalPayable = null;

    /**
     * Final payable (after discount, delivery charge, or tax application)
     * @var int|float
     */
    private $payableAmount = null;

    /**
     * Delivery charge on item
     * @var array
     */
    private $deliveryCharge = [];

    /**
     * Discounts on item
     * @var array
     */
    private $discounts = [];

    /**
     * Taxes on item
     * @var array
     */
    private $taxes = [];

    /**
     * Private constructor to initialize a new item with ID, price, and quantity.
     *
     * @param int|string $id The unique identifier of the item.
     * @param int|float $price The price of a single unit of the item.
     * @param int $quantity The quantity of the item.
     */
    private function __construct(int|string $id, int|float $price, int $quantity)
    {
        $this->id = $id;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->originalPayable = $this->payableAmount = $price * $quantity;
    }

    /**
     * Static method to create and return a new item instance.
     *
     * @param int|string $id The unique identifier of the item.
     * @param int|float $price The price of a single unit of the item.
     * @param int $quantity The quantity of the item.
     *
     * @return self
     */
    public static function add(int|string $id, int|float $price, int $quantity): self
    {
        return new self($id, $price, $quantity);
    }

    /**
     * Applies a flat discount to the item.
     * 
     * @param int|float $discount The discount amount to apply.
     *
     * @return void
     */
    public function applyFlatDiscount(int|float $discount): void
    {
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::flatDiscount($this->payableAmount, $discount);

        $this->discounts[] = [
            'type' => Discount::FLAT_TYPE,
            'discount' => $discount,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payableAmount
        ];
    }

    /**
     * Applies a percentage-based discount to the item.
     *
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto The maximum discount allowed. Defaults to 0 (no limit).
     *
     * @return void
     */
    public function applyPercentageDiscount(int|float $percentage, int|float $upto = 0): void
    {
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::percentageDiscount($this->payableAmount, $percentage, $upto);

        $this->discounts[] = [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            ...($upto > 0 ? ['upto' => $upto] : []),
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payableAmount
        ];
    }

    /**
     * Applies a delivery charge to the item.
     *
     * @param int|float $charge The delivery charge to apply.
     *
     * @return void
     */
    public function applyDeliveryCharge(int|float $charge): void
    {
        $this->checkDeliveryChargeBeforeAddingNew();

        $beforeDeliveryCharge = $this->payableAmount;
        $this->payableAmount = $this->payableAmount + $charge;

        $this->deliveryCharge[] = [
            'beforeDeliveryCharge' => $beforeDeliveryCharge,
            'afterDeliveryCharge' => $this->payableAmount
        ];
    }

    /**
     * Applies a tax to the item.
     *
     * @param string $type The type/label of the tax.
     * @param int|float $rate The tax rate in percentage.
     *
     * @return void
     */
    public function applyTax(string $type = 'general', int|float $rate): void
    {
        $beforeTax = $this->payableAmount;
        $this->payableAmount = $this->payableAmount + (($this->payableAmount * $rate) / 100);

        $this->taxes[] = [
            'type' => $type,
            'rate' => $rate,
            'beforeTax' => $beforeTax,
            'afterTax' => $this->payableAmount
        ];
    }

    /**
     * Retrieves original payable.
     * 
     * @return int|float
     */
    public function originalPayable(): int|float
    {
        return $this->originalPayable;
    }

    /**
     * Retrieves final payable.
     * 
     * @return int|float
     */
    public function payableAmount(): int|float
    {
        return $this->payableAmount;
    }


    /**
     * Retrieves a detailed breakdown of the item's pricing and modifications.
     *
     * @return array
     */
    private function getDetail(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discounts' => count($this->discounts) > 0 ? $this->discounts : null,
            'taxes' => count($this->taxes) > 0 ? $this->taxes : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'originalPayable' => round($this->originalPayable, 2),
            'payableAmount' => round($this->payableAmount, 2),
        ];
    }
}
