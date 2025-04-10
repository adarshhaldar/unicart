<?php

namespace Unicart\Validators;

use Exception;

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
        'applyingDeliveryCharge'
    ];

    /**
     * Validates addition of new item's price
     * 
     * @param mixed $id Unique identifier for item.
     * @param int|float $price Price of the item.
     * 
     * @return void
     */
    private function checkPrice(int|string $id, int|float $price): void
    {
        if ($price <= 0) {
            throw new Exception('Price for item with Id: ' . $id . ' can not be less than or equal to 0.');
        }
    }

    /**
     * Validates addition of new item's quantity
     * 
     * @param mixed $id Unique identifier for item.
     * @param int $quantity Quantity of the item.
     * 
     * @return void
     */
    private function checkQuantity(int|string $id, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity for item with Id: ' . $id . ' can not be less than or equal to 0.');
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
            throw new Exception('Upto discount amount for Item with Id: ' . $this->id . ' is invalid.');
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
            throw new Exception('Can not add discount after taxation for item with Id: ' . $this->id);
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
            throw new Exception('Can not add discount after adding delivery charge for item with Id: ' . $this->id);
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
            throw new Exception('Can not add another delivery charge for item with Id: ' . $this->id);
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
            throw new Exception('Can not add BxGy after adding BxGy for item with Id: ' . $this->id);
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
            throw new Exception('Can not add BxGy after adding other discounts for item with Id: ' . $this->id);
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
            throw new Exception('Can not add discount after adding BxGy for item with Id: ' . $this->id);
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
            throw new Exception('BxGy buy or get quantity can not be negative for item with Id: ' . $this->id);
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
            throw new Exception('Quantity does not satisfy BxGy buy and get quantities for item with Id: ' . $this->id);
        }
    }

    private function validateApplyingDeliveryCharge()
    {
        $this->checkDeliveryChargeBeforeAddingNew();
    }

    /**
     * Validates application of BxGy discount
     * 
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validateApplyingBxGy($xQuantity, $yQuantity)
    {
        $this->checkBxGyQuantity($xQuantity, $yQuantity);
        $this->checkItemQuantityForBxGy($xQuantity, $yQuantity);
        $this->checkBxGyBeforeApplyingNewBxGy();
        $this->checkDiscountBeforeApplyingBxGy();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();
    }

    /**
     * Validates application of percentage-based discount
     * 
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscount($upto): void
    {
        $this->checkBxGyBeforeApplyingDiscount();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();
        $this->checkUptoAmount($upto);
    }

    /**
     * Validates applications of flat discount
     * 
     * @return void
     */
    private function validateApplyingFlatDiscount(): void
    {
        $this->checkBxGyBeforeApplyingDiscount();
        $this->checkTaxBeforeApplyingDiscount();
        $this->checkDeliveryChargeBeforeApplyingDiscount();
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
        $this->checkPrice($id, $price);
        $this->checkQuantity($id, $quantity);
    }

    /**
     * Validates item before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param mixed $id Unique identifier for item.
     * @param int|float $price Price of the item.
     * @param int $quantity Quantity of the item.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validate(string $for, mixed $id = null, int|float $price = 0, int $quantity = 0, int|float $upto = 0, int $xQuantity = 0, int $yQuantity = 0): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new Exception('Invalid validator for item validation');
        }

        match ($for) {
            'addingItem' => $this->validateAddingItem($id, $price, $quantity),
            'applyingFlatDiscount' => $this->validateApplyingFlatDiscount(),
            'applyingPercentageDiscount' => $this->validateApplyingPercentageDiscount($upto),
            'applyingBxGy' => $this->validateApplyingBxGy($xQuantity, $yQuantity),
            'applyingDeliveryCharge' => $this->validateApplyingDeliveryCharge()
        };
    }
}
