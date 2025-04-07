<?php

namespace Unicart\Classes;

use Exception;
use Unicart\Checks\ItemCheck;

class Item
{
    use ItemCheck;

    private $id = null;
    private $price = null;
    private $quantity = null;
    private $initialPayable = null;
    private $payable = null;
    private $deliveryCharge = [];
    private $discounts = [];
    private $taxes = [];

    private function __construct(int|string $id, int|float $price, int $quantity)
    {
        $this->id = $id;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->initialPayable = $this->payable = $price * $quantity;
    }

    public static function add(int|string $id, int|float $price, int $quantity)
    {
        return new self($id, $price, $quantity);
    }

    public function applyFlatDiscount(int|float $discount)
    {
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $beforeDiscount = $this->payable;
        $this->payable = Discount::flatDiscount($this->payable, $discount);

        $this->discounts[] = [
            'type' => Discount::FLAT_TYPE,
            'discount' => $discount,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payable
        ];
    }

    public function applyPercentageDiscount(int|float $percentage, int|float $upto)
    {
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $beforeDiscount = $this->payable;
        $this->payable = Discount::percentageDiscount($this->payable, $percentage, $upto);

        $this->discounts[] = $upto > 0 ? [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            'upto' => $upto,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payable
        ] : [
            'type' => Discount::PERCENTAGE_TYPE,
            'discount' => $percentage,
            'beforeDiscount' => $beforeDiscount,
            'afterDiscount' => $this->payable
        ];
    }

    public function applyDeliveryCharge(int|float $charge)
    {
        $this->checkDeliveryChargeBeforeAddingNew();

        $beforeDeliveryCharge = $this->payable;
        $this->payable = $this->payable + $charge;

        $this->deliveryCharge[] = [
            'beforeDeliveryCharge' => $beforeDeliveryCharge,
            'afterDeliveryCharge' => $this->payable
        ];
    }

    public function applyTax(string $type, int|float $rate)
    {
        $beforeTax = $this->payable;
        $this->payable = $this->payable + (($this->payable * $rate) / 100);

        $this->taxes[] = [
            'type' => $type,
            'rate' => $rate,
            'beforeTax' => $beforeTax,
            'afterTax' => $this->payable
        ];
    }

    public function detail()
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discounts' => count($this->discounts) > 0 ? $this->discounts : null,
            'taxes' => count($this->taxes) > 0 ? $this->taxes : null,
            'deliveryCharge' => count($this->deliveryCharge) > 0 ? $this->deliveryCharge : null,
            'initialPayable' => round($this->initialPayable, 2),
            'finalPayable' => round($this->payable, 2),
        ];
    }
}
