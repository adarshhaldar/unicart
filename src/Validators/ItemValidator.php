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
     * Validates item before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validate(string $for, int|float $upto = 0): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new Exception('Invalid validator for item validation');
        }

        switch ($for) {
            case 'applyingFlatDiscount':
                $this->checkTaxBeforeApplyingDiscount();
                $this->checkDeliveryChargeBeforeApplyingDiscount();
                break;
            case 'applyingPercentageDiscount':
                $this->checkTaxBeforeApplyingDiscount();
                $this->checkDeliveryChargeBeforeApplyingDiscount();
                $this->checkUptoAmount($upto);
                break;
            case 'applyingDeliveryCharge':
                $this->checkDeliveryChargeBeforeAddingNew();
                break;
            default:
                return;
        }
    }
}
