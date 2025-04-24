<?php

namespace Unicart\Validators;

use Unicart\Classes\Locale;
use Unicart\Exceptions\ItemException;

trait ItemValidator
{
    /**
     * Validators for item validation before any application
     */
    const VALIDATORS = [
        'addingItem',
        'applyingFlatDiscount',
        'applyingPercentageDiscount',
        'applyingBxGy',
        'applyingDeliveryCharge',
        'applyingTax'
    ];

    /**
     * Checks new item's id
     * 
     * @param int|string $id Unique identifier for item.
     * 
     * @return void
     */
    private function checkId(int|string $id): void
    {
        if (is_float($id)) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_id', ['id' => $id]));
        }
    }

    /**
     * Checks new item's price
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $price Price of the item.
     * 
     * @return void
     */
    private function checkPrice(int|string $id, int|float $price): void
    {
        if ($price <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_price', ['id' => $id]));
        }
    }

    /**
     * Checks new item's quantity
     * 
     * @param int|string $id Unique identifier for item.
     * @param int $quantity Quantity of the item.
     * 
     * @return void
     */
    private function checkQuantity(int|string $id, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_qty', ['id' => $id]));
        }
    }

    /**
     * Checks if upto amount is valid.
     * 
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function checkUptoAmount(int|float $upto): void
    {
        if ($upto < 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_upto_amount', ['id' => $this->id]));
        }
    }

    /**
     * Checks tax before applying discount on item.
     * 
     * @return void
     */
    private function checkTaxBeforeApplyingDiscount(): void
    {
        if (count($this->taxes) > 0) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_discount_after_tax', ['id' => $this->id]));
        }
    }

    /**
     * Checks delivery charge before applying discount on item.
     * 
     * @return void
     */
    private function checkDeliveryChargeBeforeApplyingDiscount(): void
    {
        if (count($this->deliveryCharge) > 0) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_discount_after_delivery_charge', ['id' => $this->id]));
        }
    }

    /**
     * Checks delivery charge before applying another one on item.
     * 
     * @return void
     */
    private function checkDeliveryChargeBeforeAddingNew(): void
    {
        if (count($this->deliveryCharge) > 0) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_delivery_charge', ['id' => $this->id]));
        }
    }

    /**
     * Checks bxgy before applying another one on item.
     * 
     * @return void
     */
    private function checkBxGyBeforeApplyingNewBxGy(): void
    {
        if ($this->isBxGyApplied) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_bxgy_after_bxgy', ['id' => $this->id]));
        }
    }

    /**
     * Checks discount before applying bxgy on item.
     * 
     * @return void
     */
    private function checkDiscountBeforeApplyingBxGy(): void
    {
        if (count($this->discounts) > 0) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_bxgy_after_dicount', ['id' => $this->id]));
        }
    }

    /**
     * Checks bxgy before applying discount on item.
     * 
     * @return void
     */
    private function checkBxGyBeforeApplyingDiscount(): void
    {
        if ($this->isBxGyApplied) {
            throw new ItemException(Locale::translate('item_exceptions.cannt_add_discount_after_bxgy', ['id' => $this->id]));
        }
    }

    /**
     * Checks if both the buy and get quantites are valid.
     * 
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * 
     * @return void
     */
    private function checkBxGyQuantity(int $xQuantity, int $yQuantity): void
    {
        if ($xQuantity < 0 || $yQuantity < 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_bxgy_qty', ['id' => $this->id]));
        }
    }

    /**
     * Checks if the item quantity and the buy and get quantites are satisfyable.
     * 
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * 
     * @return void
     */
    private function checkItemQuantityForBxGy(int $xQuantity, int $yQuantity): void
    {
        $itemQuantity = $this->quantity;
        if ($itemQuantity < ($xQuantity + $yQuantity)) {
            throw new ItemException(Locale::translate('item_exceptions.dissatified_bxgy_qty', ['id' =>  $this->id]));
        }
    }

    /**
     * Checks the stacking of discount
     * 
     * @return void
     */
    private function checkDiscountStacking(): void
    {
        if (!$this->allowDiscountStacking && $this->hasDiscount) {
            throw new ItemException(Locale::translate('item_exceptions.discount_stacking_disabled'));
        }
    }

    /**
     * Checks the discount percentage
     * 
     * @param int|float $percentage The discount percentage.
     * 
     * @return void
     */
    private function checkDiscountPercentage(int|float $percentage): void
    {
        if ($percentage <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_discount_percentage', ['id' => $this->id]));
        }
    }

    /**
     * Checks the discount amount
     * 
     * @param int|float $discount The discount amount.
     * 
     * @return void
     */
    private function checkDiscountAmount(int|float $discount): void
    {
        if ($discount <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_discount', ['id' => $this->id]));
        }
    }

    /**
     * Checks the delivery charge
     * 
     * @param int|float $charge The delivery charge.
     * 
     * @return void
     */
    private function checkDeliveryCharge(int|float $charge): void
    {
        if ($charge <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_delivery_charge', ['id' => $this->id]));
        }
    }

    /**
     * Checks the tax rate
     * 
     * @param int|float $rate The tax rate in %.
     * 
     * @return void
     */
    private function checkTaxRate(int|float $rate): void
    {
        if ($rate <= 0) {
            throw new ItemException(Locale::translate('item_exceptions.invalid_tax', ['id' => $this->id]));
        }
    }

    /**
     * Validates application of delivery charge
     * 
     * @param int|float $rate The tax rate in %.
     * 
     * @return void
     */
    private function validateApplyingDeliveryCharge(int|float $charge): void
    {
        $this->checkDeliveryChargeBeforeAddingNew();

        $this->checkDeliveryCharge($charge);
    }

    /**
     * Validates application of BxGy discount
     * 
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validateApplyingBxGy(int $xQuantity, int $yQuantity): void
    {
        $this->checkBxGyQuantity($xQuantity, $yQuantity);
        $this->checkItemQuantityForBxGy($xQuantity, $yQuantity);
        $this->checkBxGyBeforeApplyingNewBxGy();
        $this->checkDiscountBeforeApplyingBxGy();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $this->checkDiscountStacking();
        if (!$this->hasDiscount) {
            $this->hasDiscount = true;
        }
    }

    /**
     * Validates application of percentage-based discount
     * 
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscount(int|float $percentage, int|float $upto): void
    {
        $this->checkBxGyBeforeApplyingDiscount();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();
        $this->checkUptoAmount($upto);

        $this->checkDiscountStacking();
        if (!$this->hasDiscount) {
            $this->hasDiscount = true;
        }

        $this->checkDiscountPercentage($percentage);
    }

    /**
     * Validates applications of flat discount
     * 
     * @param int|float $discount The discount amount.
     * 
     * @return void
     */
    private function validateApplyingFlatDiscount(int|float $discount): void
    {
        $this->checkBxGyBeforeApplyingDiscount();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();

        $this->checkDiscountStacking();
        if (!$this->hasDiscount) {
            $this->hasDiscount = true;
        }

        $this->checkDiscountAmount($discount);
    }

    /**
     * Validates the tax rate
     * 
     * @param int|float $rate The tax rate in %.
     * 
     * @return void
     */
    private function validateApplyingTax(int|float $rate): void
    {
        $this->checkTaxRate($rate);
    }

    /**
     * Validates item creation
     * 
     * @param int|float $price Price of the item.
     * @param int $quantity Quantity of the item.
     * 
     * @return void
     */
    private function validateAddingItem(int|string $id, int|float $price, int $quantity): void
    {
        $this->checkId($id);
        $this->checkPrice($id, $price);
        $this->checkQuantity($id, $quantity);
    }

    /**
     * Fetch variable and their data from param array
     * 
     * @param array $params List of parameters
     * 
     * @return array
     */
    private function getVariablesFromParams(array $params): array
    {
        return [
            $params['id'] ?? null,
            $params['price'] ?? null,
            $params['quantity'] ?? null,
            $params['discount'] ?? null,
            $params['percentage'] ?? null,
            $params['upto'] ?? null,
            $params['charge'] ?? null,
            $params['rate'] ?? null,
            $params['xQuantity'] ?? null,
            $params['yQuantity'] ?? null,
            $params['spend'] ?? null,
            $params['get'] ?? null
        ];
    }

    /**
     * Validates item before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param array $params List of parameters.
     * 
     * @return void
     */
    private function validate(string $for, array $params = []): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new ItemException(Locale::translate('invalid_validator'));
        }

        list($id, $price, $quantity, $discount, $percentage, $upto, $charge, $rate, $xQuantity, $yQuantity) = $this->getVariablesFromParams($params);

        match ($for) {
            'addingItem' => $this->validateAddingItem($id, $price, $quantity),
            'applyingFlatDiscount' => $this->validateApplyingFlatDiscount($discount),
            'applyingPercentageDiscount' => $this->validateApplyingPercentageDiscount($percentage, $upto),
            'applyingBxGy' => $this->validateApplyingBxGy($xQuantity, $yQuantity),
            'applyingDeliveryCharge' => $this->validateApplyingDeliveryCharge($charge),
            'applyingTax' => $this->validateApplyingTax($rate)
        };
    }
}
