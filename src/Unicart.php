<?php

namespace Unicart;

use Exception;
use Unicart\Classes\Discount;
use Unicart\Classes\Item;
use Unicart\Checks\UnicartCheck;

class Unicart
{
    use UnicartCheck;

    private $cartItems = [];
    private $deliveryCharge = [];
    private $discounts = [];
    private $taxes = [];
    private $hasCartApplicationInitiated = false;
    private $finalPayable = null;

    private function items()
    {
        $cart = [];

        foreach ($this->cartItems as $cartItem) {
            $cart[] = $cartItem->detail();
        }

        return $cart;
    }

    private function getCartSummary()
    {
        $beforeTotal = 0;
        $afterTotal = 0;

        foreach ($this->cartItems as $item) {
            $details = $item->detail();
            $beforeTotal += $details['initialPayable'];
            $afterTotal += $details['finalPayable'];
        }

        return [
            'discounts' => count($this->discounts) > 0 ? $this->discounts : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'initialPayable' => round($beforeTotal, 2),
            'finalPayable' => round($this->finalPayable ?? $afterTotal, 2)
        ];
    }

    public function addItem(int|string $id, int|float $price, int $quantity = 1)
    {
        $this->checkHasCartInitiated($id, 'new item');
        $this->checkItemExist($id);

        $this->cartItems[$id] = Item::add($id, $price, $quantity);
        return $this;
    }

    public function addFlatDiscountOnItem(int|string $id, int|float $discount)
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkItemDoesNotExist($id);

        $this->cartItems[$id]->applyFlatDiscount($discount);
        return $this;
    }

    public function addPercentageDiscountOnItem(int|string $id, int|float $percentage, int|float $upto = 0)
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkUptoAmount($id, $upto);
        $this->checkItemDoesNotExist($id);

        $this->cartItems[$id]->applyPercentageDiscount($percentage, $upto);
        return $this;
    }

    public function addDeliveryChargeOnItem(int|string $id, int|float $charge)
    {
        $this->checkHasCartInitiated($id, 'delivery charge');
        $this->checkItemDoesNotExist($id);

        $this->cartItems[$id]->applyDeliveryCharge($charge);

        return $this;
    }

    public function addTaxOnItem(int|string $id, int|float $rate, string $type = 'general')
    {
        $this->checkHasCartInitiated($id, 'tax');
        $this->checkItemDoesNotExist($id);

        $this->cartItems[$id]->applyTax($type, $rate);

        return $this;
    }

    public function addFlatDiscountOnCart(int|float $discount)
    {
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();

        if ($this->finalPayable) {
            $initialPayable = $finalPayable = $this->finalPayable;
            $finalPayable = Discount::flatDiscount($finalPayable, $discount);
        } else {
            $cartSummary = $this->getCartSummary();
            $initialPayable = $finalPayable = $cartSummary['finalPayable'];

            $this->finalPayable = Discount::flatDiscount($finalPayable, $discount);
        }

        $this->discounts[] = [
            'type' => Discount::FLAT_TYPE,
            'discount' => $discount,
            'beforeDiscount' => $initialPayable,
            'afterDiscount' => $this->finalPayable
        ];
        return $this;
    }

    public function addPercentageDiscountOnCart(int|float $percentage, int|float $upto = 0)
    {
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();
        $this->checkUptoAmountForCart($upto);

        if ($this->finalPayable) {
            $initialPayable = $finalPayable = $this->finalPayable;
            $this->finalPayable = Discount::percentageDiscount($finalPayable, $percentage, $upto);
        } else {
            $cartSummary = $this->getCartSummary();
            $initialPayable = $finalPayable = $cartSummary['finalPayable'];

            $this->finalPayable = Discount::percentageDiscount($finalPayable, $percentage, $upto);
        }

        $this->discounts[] = $upto > 0 ? [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            'upto' => $upto,
            'beforeDiscount' => $initialPayable,
            'afterDiscount' => $this->finalPayable
        ] : [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            'beforeDiscount' => $initialPayable,
            'afterDiscount' => $this->finalPayable
        ];

        return $this;
    }

    public function addDeliveryChargeOnCart(int|float $charge)
    {
        $this->checkItemLevelApplications('delivery charge');
        $this->checkDeliveryChargeBeforeAddingNew();

        $initialPayable = $finalPayable = $this->finalPayable ?? $this->getCartSummary()['finalPayable'];
        $this->finalPayable = $finalPayable + $charge;

        $this->deliveryCharge[] = [
            'beforeDeliveryCharge' => $initialPayable,
            'afterDeliveryCharge' => $this->finalPayable
        ];

        return $this;
    }

    public function addTaxOnCart(int|float $rate, string $type = 'general')
    {
        $this->checkItemLevelApplications('tax');

        $initialPayable = $finalPayable = $this->finalPayable ?? $this->getCartSummary()['finalPayable'];

        $this->finalPayable = $finalPayable + (($finalPayable * $rate) / 100);

        $this->taxes[] = [
            'type' => $type,
            'rate' => $rate,
            'beforeTax' => $initialPayable,
            'afterTax' => $this->finalPayable
        ];

        return $this;
    }


    public function summary()
    {
        return [
            'items' => $this->items(),
            ...$this->getCartSummary()
        ];
    }
}
