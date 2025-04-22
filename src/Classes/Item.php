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
     * Flag for checking if item has discount
     * @var bool
     */
    private $hasDiscount = false;

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
     * Meta data of item
     * @var array
     */
    private $metadata = [];

    /**
     * Flag for allowing discount to stack
     * @var bool
     */
    private $allowDiscountStacking = true;

    /**
     * Rounding method for output
     * @var string
     */
    private $roundingMode = 'round';

    /**
     * Public constructor to initialize a new item with ID, price, and quantity.
     *
     * @param int|string $id The unique identifier of the item.
     * @param int|float $price The price of a single unit of the item.
     * @param int $quantity The quantity of the item, defaults to 1.
     * @param string $roundingMode The round off mode. Default set to round. Valid modes are round,floor,ceil
     */
    public function __construct(int|string $id, int|float $price, int $quantity = 1, string $roundingMode = 'round')
    {
        $this->validate('addingItem', ['id' => $id, 'price' => $price, 'quantity' => $quantity]);

        $this->roundingMode = $roundingMode;

        $this->id = $id;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->originalPayable = $this->payableAmount = $price * $quantity;
    }

    /**
     * Add metadata
     * 
     * @param array $metadata The metadata to add for the item.
     * 
     * @return self
     */
    public function addMetaData(array $metadata = []): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Set permission for discount stacking
     * 
     * @param bool $allowDiscountStacking The flag to allow/disallow discount stacking. Default set to true.
     * 
     * @return self
     */
    public function setDiscountStacking(bool $allowDiscountStacking = true): self
    {
        $this->allowDiscountStacking = $allowDiscountStacking;

        return $this;
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
        $this->validate('applyingFlatDiscount', ['discount' => $discount]);

        $beforeDiscount = $this->payableAmount;
        $this->payableAmount = Discount::flatDiscount($this->payableAmount, $discount * $this->quantity);

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
        $this->validate('applyingPercentageDiscount', ['percentage' => $percentage, 'upto' => $upto]);

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
        $this->validate('applyingBxGy', ['xQuantity' => $xQuantity, 'yQuantity' => $yQuantity]);

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
        $this->validate('applyingDeliveryCharge', ['charge' => $charge]);

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
     * @param int|float $rate The tax rate in percentage.
     * @param string $type The type/label of the tax.
     * 
     * @return self
     */
    public function applyTax(int|float $rate, string $type = 'general'): self
    {
        $this->validate('applyingTax', ['rate' => $rate]);

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
     * Executes the given action if the condition evaluates to true
     * 
     * @param bool|callable $condition A boolean value or a callable returning a boolean.
     * @param callable $action The action to execute if the condition is true.
     * 
     * @return self
     */
    public function when(bool|callable $condition, callable $action): self
    {
        if (is_callable($condition) ? $condition() : $condition) {
            $action();
        }
        return $this;
    }

    /**
     * Executes the given action if the condition evaluates to false
     * 
     * @param bool|callable $condition A boolean value or a callable returning a boolean.
     * @param callable $action The action to execute if the condition is false.
     * 
     * @return self
     */
    public function unless(bool|callable $condition, callable $action): self
    {
        if (!(is_callable($condition) ? $condition() : $condition)) {
            $action();
        }
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
        $detail = [
            'id' => $this->id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discounts' => count($this->discounts) > 0 ? $this->discounts : null,
            'taxes' => count($this->taxes) > 0 ? $this->taxes : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'originalPayable' => $this->roundValue($this->roundingMode, $this->originalPayable),
            'payableAmount' => $this->roundValue($this->roundingMode, $this->payableAmount),
        ];

        if (count($this->metadata) > 0) {
            $detail['metadata'] = $this->metadata;
        }

        return $detail;
    }
}
