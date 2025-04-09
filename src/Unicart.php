<?php

namespace Unicart;

use Unicart\Classes\Discount;
use Unicart\Classes\Item;
use Unicart\Formats\OutputFormat;
use Unicart\Validators\UnicartValidator;

class Unicart
{
    use UnicartValidator, OutputFormat;

    /**
     * Cart items
     * @var array
     */
    private $cartItems = [];

    /**
     * Delivery charge on cart
     * @var array
     */
    private $deliveryCharge = [];

    /**
     * Discounts on cart
     * @var array
     */
    private $discounts = [];

    /**
     * Taxes on cart
     * @var array
     */
    private $taxes = [];

    /**
     * Flag for checking whether cart methods has been initiated
     * @var bool
     */
    private $hasCartApplicationInitiated = false;

    /**
     * Final payable amount (after discount, delivery charge, or tax application)
     * @var int|float
     */
    private $payableAmount = null;

    /**
     * Adds a new item to the cart.
     * 
     * @param int|string $id A Unique identifier for the item.
     * @param int|float $price The price of a single unit of the item.
     * @param int $quantity The quantity of the item to be added. Defaults to 1.
     * 
     * @return self
     */
    public function addItem(int|string $id, int|float $price, int $quantity = 1): self
    {
        $this->validate('addingItem', $id);

        $this->cartItems[$id] = Item::add($id, $price, $quantity);
        return $this;
    }

    /**
     * Applies a flat discount on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int|float $discount The discount amount.
     * 
     * @return self
     */
    public function applyFlatDiscountOnItem(int|string $id, int|float $discount): self
    {
        $this->validate('applyingFlatDiscountOnItem', $id);

        $this->cartItems[$id]->applyFlatDiscount($discount);
        return $this;
    }

    /**
     * Applies a percentage-based discount on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto [optional] The maximum discount allowed. Defaults to 0 (no limit).
     * 
     * @return self
     */
    public function applyPercentageDiscountOnItem(int|string $id, int|float $percentage, int|float $upto = 0): self
    {
        $this->validate('applyingPercentageDiscountOnItem', $id, $upto);

        $this->cartItems[$id]->applyPercentageDiscount($percentage, $upto);
        return $this;
    }

    /**
     * Applies a bxgy-based discount on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * @param string $label The to describe the bxgy discount.
     * 
     * @return self
     */
    public function applyBxGyOnItem(int|string $id, int $xQuantity, int $yQuantity, string $label = 'bxgy')
    {
        $this->validate('applyingBxGyOnItem', $id, 0, $xQuantity, $yQuantity);

        $this->cartItems[$id]->applyBxGy($xQuantity, $yQuantity, $label);
        return $this;
    }

    /**
     * Applies a delivery charge on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int|float $charge The delivery charge.
     * 
     * @return self
     */
    public function applyDeliveryChargeOnItem(int|string $id, int|float $charge): self
    {
        $this->validate('applyingDeliveryChargeOnItem', $id);

        $this->cartItems[$id]->applyDeliveryCharge($charge);

        return $this;
    }

    /**
     * Applies a tax on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int|float $rate The tax rate in %.
     * @param string $type The type of tax being applied on the item.
     * 
     * @return self
     */
    public function applyTaxOnItem(int|string $id, int|float $rate, string $type = 'general'): self
    {
        $this->validate('applyingTaxOnItem', $id);

        $this->cartItems[$id]->applyTax($type, $rate);

        return $this;
    }

    /**
     * Applies a flat discount on the cart.
     * 
     * @param int|float $discount The discount amount.
     * 
     * @return self
     */
    public function applyFlatDiscountOnCart(int|float $discount): self
    {
        $this->validate('applyingFlatDiscountOnCart');

        $originalPayable = $payableAmount = $this->payableAmount();
        $this->payableAmount = Discount::flatDiscount($payableAmount, $discount);

        $this->discounts[] = [
            'type' => Discount::FLAT_TYPE,
            'discount' => $discount,
            'beforeDiscount' => $originalPayable,
            'afterDiscount' => $this->payableAmount
        ];
        return $this;
    }

    /**
     * Applies a percentage-based discount on the cart.
     * 
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto [optional] The maximum discount allowed. Defaults to 0 (no limit).
     * 
     * @return self
     */
    public function applyPercentageDiscountOnCart(int|float $percentage, int|float $upto = 0): self
    {
        $this->validate('applyingPercentageDiscountOnCart', null, $upto);

        $originalPayable = $payableAmount = $this->payableAmount();
        $this->payableAmount = Discount::percentageDiscount($payableAmount, $percentage, $upto);

        $this->discounts[] = [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            ...($upto > 0 ? ['upto' => $upto] : []),
            'beforeDiscount' => $originalPayable,
            'afterDiscount' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a delivery charge on the cart.
     * 
     * @param int|float $charge The delivery charge.
     * 
     * @return self
     */
    public function applyDeliveryChargeOnCart(int|float $charge): self
    {
        $this->validate('applyingDeliveryChargeOnCart');

        $originalPayable = $payableAmount = $this->payableAmount ?? $this->summary()['payableAmount'];
        $this->payableAmount = $payableAmount + $charge;

        $this->deliveryCharge[] = [
            'beforeDeliveryCharge' => $originalPayable,
            'afterDeliveryCharge' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Applies a tax on the cart.
     * 
     * @param int|float $rate The tax rate in %.
     * @param string $type The type of tax being applied on the cart.
     * 
     * @return self
     */
    public function applyTaxOnCart(int|float $rate, string $type = 'general'): self
    {
        $this->validate('applyingTaxOnCart');

        $originalPayable = $payableAmount = $this->payableAmount ?? $this->summary()['payableAmount'];

        $this->payableAmount = $payableAmount + (($payableAmount * $rate) / 100);

        $this->taxes[] = [
            'type' => $type,
            'rate' => $rate,
            'beforeTax' => $originalPayable,
            'afterTax' => $this->payableAmount
        ];

        return $this;
    }

    /**
     * Retrieves the list of items currently in the cart.
     * 
     * @param string $as Flag to fetch data in different formats. Accepted formats are array, object, json
     * 
     * @return mixed
     */
    public function items(string $as = 'array'): mixed
    {
        $cart = [];

        foreach ($this->cartItems as $cartItem) {
            $cart[] = $cartItem->toArray();
        }

        return $this->formatter($as, count($cart) > 0 ? $cart : null);
    }

    /**
     * Retrieves the item using the unique identifier.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * 
     * @return object
     */

    public function item(int|string $id): object
    {
        $this->checkItemDoesNotExist($id);
        return $this->cartItems[$id];
    }

    /**
     * Retrieves original payable of the cart.
     * 
     * @return int|float
     */
    public function originalPayable(): int|float
    {
        return $this->summary()['originalPayable'];
    }

    /**
     * Retrieves final payable of the cart.
     * 
     * @return int|float
     */
    public function payableAmount(): int|float
    {
        return $this->payableAmount ?? $this->summary()['payableAmount'];
    }

    /**
     * Calculates and returns a summary of the entire cart.
     * 
     * @return array
     */
    private function summary(): array
    {
        $beforeTotal = 0;
        $afterTotal = 0;

        foreach ($this->cartItems as $item) {
            $details = $item->toArray();
            $beforeTotal += $details['originalPayable'];
            $afterTotal += $details['payableAmount'];
        }

        return [
            'discounts' => count($this->discounts) > 0 ? $this->discounts : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'originalPayable' => round($beforeTotal, 2),
            'payableAmount' => round($this->payableAmount ?? $afterTotal, 2)
        ];
    }

    /**
     * Cart cart detail
     * 
     * @return array
     */
    private function getDetail(): array
    {
        return [
            'items' => $this->items(),
            ...$this->summary()
        ];
    }
}
