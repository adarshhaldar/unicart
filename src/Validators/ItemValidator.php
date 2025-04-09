<?php

namespace Unicart\Validators;

use Exception;

trait ItemValidator
{
    /**
     * Validators for item validation before any application
     */
    const VALIDATORS = [
        'applyingFlatDiscount',
        'applyingPercentageDiscount',
        'applyingBogo',
        'applyingDeliveryCharge'
    ];

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

    /**
     * Validates item before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validate(string $for, int|float $upto = 0, int $xQuantity = 0, mixed $yQuantity = 0): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new Exception('Invalid validator for item validation');
        }

        switch ($for) {
            case 'applyingFlatDiscount':
                $this->checkBxGyBeforeApplyingDiscount();
                $this->checkTaxBeforeApplyingDiscount();
                $this->checkDeliveryChargeBeforeApplyingDiscount();
                break;
            case 'applyingPercentageDiscount':
                $this->checkBxGyBeforeApplyingDiscount();
                $this->checkTaxBeforeApplyingDiscount();
                $this->checkDeliveryChargeBeforeApplyingDiscount();
                $this->checkUptoAmount($upto);
                break;
            case 'applyingBogo':
                $this->checkBxGyQuantity($xQuantity, $yQuantity);
                $this->checkItemQuantityForBxGy($xQuantity, $yQuantity);
                $this->checkBxGyBeforeApplyingNewBxGy();
                $this->checkDiscountBeforeApplyingBxGy();
                $this->checkTaxBeforeApplyingDiscount();
                $this->checkDeliveryChargeBeforeApplyingDiscount();
                break;
            case 'applyingDeliveryCharge':
                $this->checkDeliveryChargeBeforeAddingNew();
                break;
            default:
                return;
        }
    }
}
