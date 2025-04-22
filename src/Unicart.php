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
     * Flag for checking if any item has discount
     * @var bool
     */
    private $anyItemHasDiscount = false;

    /**
     * Delivery charge on cart
     * @var array
     */
    private $deliveryCharge = [];

    /**
     * Flag for checking if cart has discount
     * @var bool
     */
    private $cartHasDiscount = false;

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
     * Flag for checking whether sxgy discount is applied on cart
     * @var bool
     */
    private $isSxGyApplied = false;

    /**
     * Final payable amount (after discount, delivery charge, or tax application)
     * @var int|float
     */
    private $payableAmount = null;

    /**
     * Flag for allowing item to get override
     * @var bool
     */
    private $allowItemOverride = false;

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
     * Set permission for item override
     * 
     * @param bool $allowItemOverride The flag to allow/disallow item overriding. Default set to false.
     * 
     * @return self
     */
    public function setItemOverriding(bool $allowItemOverride = false): self
    {
        $this->allowItemOverride = $allowItemOverride;

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
     * Set round off method for output
     * 
     * @param string $roundingMode The round off mode. Default set to round. Valid modes are round,floor,ceil.
     * 
     * @return self
     */
    public function setRoundingMode(string $roundingMode = 'round'): self
    {
        $this->roundingMode = $roundingMode;

        return $this;
    }

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
        $this->validate('addingItem', ['id' => $id, 'price' => $price, 'quantity' => $quantity]);

        $item = new Item($id, $price, $quantity);
        $this->cartItems[$id] = $item;
        return $this;
    }

    /**
     * Add metadata for specific item
     * 
     * @param int|string $id A Unique identifier for the item.
     * @param array $metadata The metadata to add for the item.
     * 
     * @return self
     */
    public function addItemMetaData(int|string $id, array $metadata = []): self
    {
        $this->checkItemDoesNotExist($id);
        $this->cartItems[$id]->addMetaData($metadata);

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
        $this->validate('applyingFlatDiscountOnItem', ['id' => $id, 'discount' => $discount]);

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
        $this->validate('applyingPercentageDiscountOnItem', ['id' => $id, 'percentage' => $percentage, 'upto' => $upto]);

        $this->cartItems[$id]->applyPercentageDiscount($percentage, $upto);
        return $this;
    }

    /**
     * Applies a bxgy-based discount on existing item.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * @param string $label A label to describe the BxGy discount.
     * 
     * @return self
     */
    public function applyBxGyOnItem(int|string $id, int $xQuantity, int $yQuantity, string $label = 'bxgy'): self
    {
        $this->validate('applyingBxGyOnItem', ['id' => $id, 'xQuantity' => $xQuantity, 'yQuantity' => $yQuantity]);

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
        $this->validate('applyingDeliveryChargeOnItem', ['id' => $id, 'charge' => $charge]);

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
        $this->validate('applyingTaxOnItem', ['id' => $id, 'rate' => $rate]);

        $this->cartItems[$id]->applyTax($rate, $type);

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
        $this->validate('applyingFlatDiscountOnCart', ['discount' => $discount]);

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
        $this->validate('applyingPercentageDiscountOnCart', ['percentage' => $percentage, 'upto' => $upto]);

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
        $this->validate('applyingDeliveryChargeOnCart', ['charge' => $charge]);

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
        $this->validate('applyingTaxOnCart', ['rate' => $rate]);

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
     * Applies a sxgy-based discount on cart.
     * 
     * @param int|float $spend The cart expenditure.
     * @param int|float $get The off amount.
     * @param string $label A label to describe the SxGy discount.
     * 
     * @return self
     */
    public function applySpendxGetyOffOnCart(int|float $spend, int|float $get, string $label = 'spendXgetY'): self
    {
        $this->validate('applyingSpendXGetYOffOnCart', ['spend' => $spend, 'get' => $get]);

        $originalPayable = $payableAmount = $this->payableAmount ?? $this->summary()['payableAmount'];

        if ($payableAmount >= $spend) {
            $this->isSxGyApplied = true;
            $this->payableAmount = Discount::flatDiscount($payableAmount, $get);

            $this->discounts[] = [
                'type' => Discount::SXGY_TYPE,
                'label' => $label,
                'beforeDiscount' => $originalPayable,
                'afterDiscount' => $this->payableAmount
            ];
        }

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
            'taxes' => count($this->taxes) > 0 ? $this->taxes : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'originalPayable' => $this->roundValue($this->roundingMode, $beforeTotal),
            'payableAmount' => $this->roundValue($this->roundingMode, $this->payableAmount ?? $afterTotal)
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
