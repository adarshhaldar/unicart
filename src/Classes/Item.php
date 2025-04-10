<?php

namespace Unicart\Classes;

use Unicart\Formats\OutputFormat;
use Unicart\Validators\ItemValidator;

class Item
{
    use ItemValidator, OutputFormat;

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
     * Flag to check if bxgy discount applied
     * @var bool
     */
    private $isBxGyApplied = false;

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
     * Public constructor to initialize a new item with ID, price, and quantity.
     *
     * @param int|string $id The unique identifier of the item.
     * @param int|float $price The price of a single unit of the item.
     * @param int $quantity The quantity of the item.
     */
    public function __construct(int|string $id, int|float $price, int $quantity)
    {
        $this->validate('addingItem', $id, $price, $quantity);

        $this->id = $id;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->originalPayable = $this->payableAmount = $price * $quantity;
    }

    /**
     * Applies a flat discount to the item.
     * 
     * @param int|float $discount The discount amount to apply.
     *
     * @return self
     */
    public function applyFlatDiscount(int|float $discount): self
    {
        $this->validate('applyingFlatDiscount');

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::flatDiscount($this->payableAmount, $discount);

        $this->discounts[] = [
            'type' => Discount::FLAT_TYPE,
            'discount' => $discount,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a percentage-based discount to the item.
     *
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto The maximum discount allowed. Defaults to 0 (no limit).
     *
     * @return self
     */
    public function applyPercentageDiscount(int|float $percentage, int|float $upto = 0): self
    {
        $this->validate('applyingPercentageDiscount', null, 0, 0, $upto);

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::percentageDiscount($this->payableAmount, $percentage, $upto);

        $this->discounts[] = [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            ...($upto > 0 ? ['upto' => $upto] : []),
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a bxgy-based discount to the item.
     * 
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * @param string $label The to describe the bxgy discount.
     * 
     * @return self
     */
    public function applyBxGy(int $xQuantity, int $yQuantity, string $label = 'bxgy'): self
    {
        $this->validate('applyingBogo', null, 0, 0, 0, $xQuantity, $yQuantity);

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::bxgy($this->price, $this->quantity, $xQuantity, $yQuantity);

        $this->discounts[] = [
            'type' => Discount::BXGY_TYPE,
            'label' => $label,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a delivery charge to the item.
     *
     * @param int|float $charge The delivery charge to apply.
     *
     * @return self
     */
    public function applyDeliveryCharge(int|float $charge): self
    {
        $this->validate('applyingDeliveryCharge');

        $beforeDeliveryCharge = $this->payableAmount;
        $this->payableAmount = $this->payableAmount + $charge;

        $this->deliveryCharge[] = [
            'beforeDeliveryCharge' => $beforeDeliveryCharge,
            'afterDeliveryCharge' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a tax to the item.
     *
     * @param string $type The type/label of the tax.
     * @param int|float $rate The tax rate in percentage.
     *
     * @return self
     */
    public function applyTax(string $type = 'general', int|float $rate): self
    {
        $beforeTax = $this->payableAmount;
        $this->payableAmount = $this->payableAmount + (($this->payableAmount * $rate) / 100);

        $this->taxes[] = [
            'type' => $type,
            'rate' => $rate,
            'beforeTax' => $beforeTax,
            'afterTax' => $this->payableAmount
        ];

        return $this;
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
