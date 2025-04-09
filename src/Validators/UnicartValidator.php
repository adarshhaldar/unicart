<?php

namespace Unicart\Validators;

use Exception;

trait UnicartValidator
{
    /**
     * Validators for item and cart validation before any application
     */
    const VALIDATORS = [
        'addingItem',
        'applyingFlatDiscountOnItem',
        'applyingPercentageDiscountOnItem',
        'applyingBxGyOnItem',
        'applyingDeliveryChargeOnItem',
        'applyingTaxOnItem',
        'applyingFlatDiscountOnCart',
        'applyingPercentageDiscountOnCart',
        'applyingDeliveryChargeOnCart',
        'applyingTaxOnCart'
    ];

    /**
     * Checks if cart is empty.
     * 
     * @return void
     */
    private function checkIsCartEmpty(): void
    {
        if (count($this->cartItems) == 0) {
            throw new Exception('Cart is empty');
        }
    }

    /**
     * Checks if item exist.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * 
     * @return void
     */
    private function checkItemExist(int|string $id): void
    {
        if (isset($this->cartItems[$id])) {
            throw new Exception('Item with Id: ' . $id . ' already exist.');
        }
    }

    /**
     * Checks if item does not exist.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * 
     * @return void
     */
    private function checkItemDoesNotExist(int|string $id): void
    {
        if (!isset($this->cartItems[$id])) {
            throw new Exception('Item with Id: ' . $id . ' does not exist.');
        }
    }

    /**
     * Checks if upto amount is valid.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function checkUptoAmount(int|string $id, int|float $upto): void
    {
        if ($upto < 0) {
            throw new Exception('Upto discount amount for Item with Id: ' . $id . ' is invalid.');
        }
    }

    /**
     * Checks if any item in cart already has tax applied.
     *
     * @param string $applying Variable to store the operational entity. 
     * 
     * @return void
     */
    private function checkAnyItemHasTaxApplied(string $applying): void
    {
        foreach ($this->items() as $item) {
            if ($item['taxes'] != null) {
                throw new Exception('Can not apply ' . $applying . ' on cart as tax has already been applied on item with Id: ' . $item['id']);
            }
        }
    }

    /**
     * Checks if any item in cart already has delivery charge applied.
     * 
     * @param string $applying Variable to store the operational entity.
     * 
     * @return void
     */
    private function checkAnyItemHasDeliveryChargeApplied(string $applying): void
    {
        foreach ($this->items() as $item) {
            if ($item['deliveryCharge'] != null) {
                throw new Exception('Can not apply ' . $applying . ' on cart as delivery charge has already been applied on item with Id: ' . $item['id']);
            }
        }
    }

    /**
     * Checks for item level applications.
     * 
     * @param string $applying Variable to store the operational entity.
     * 
     * @return void
     */
    private function checkItemLevelApplications(string $applying): void
    {
        $this->checkAnyItemHasTaxApplied($applying);
        $this->checkAnyItemHasDeliveryChargeApplied($applying);

        if (!$this->hasCartApplicationInitiated) {
            $this->hasCartApplicationInitiated = true;
        }
    }

    /**
     * Checks if cart operation has been initiated.
     * 
     * @param int|string $id A unique identifier for the existing item.
     * @param string $applying Variable to store the operational entity.
     * 
     * @return void
     */
    private function checkHasCartInitiated(int|string $id, string $applying): void
    {
        if ($this->hasCartApplicationInitiated) {
            throw new Exception('Can not add ' . $applying . ' after cart initiation on item with Id: ' . $id);
        }
    }

    /**
     * Checks if upto amount for cart is valid.
     * 
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function checkUptoAmountForCart(int|float $upto): void
    {
        if ($upto < 0) {
            throw new Exception('Upto discount amount for cart is invalid.');
        }
    }

    /**
     * Checks if tax has been applied on cart.
     * 
     * @return void
     */
    private function checkTaxHasBeenApplied(): void
    {
        if (count($this->taxes) > 0) {
            throw new Exception('Can not add discount after taxation');
        }
    }

    /**
     * Checks if delivery charge has been applied on cart before applying discount.
     * 
     * @return void
     */
    private function checkDeliveryChargeHasBeenApplied(): void
    {
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add discount after adding delivery charge');
        }
    }

    /**
     * Checks if delivery charge has been applied on cart before applying another delivery charge.
     * 
     * @return void
     */
    private function checkDeliveryChargeBeforeAddingNew(): void
    {
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add another delivery charge on the cart');
        }
    }

    /**
     * Checks if both the buy and get quantites are valid.
     * 
     * @param mixed $id Unique identifier for item based validations.
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * 
     * @return void
     */
    private function checkBxGyQuantity(int|string $id, int $xQuantity, int $yQuantity): void
    {
        if ($xQuantity < 0 || $yQuantity < 0) {
            throw new Exception('BxGy buy or get quantity can not be negative for item with Id: ' . $this->item($id)->toArray()['id']);
        }
    }

    /**
     * Checks if the item quantity and the buy and get quantites are satisfyable.
     * 
     * @param mixed $id Unique identifier for item based validations.
     * @param int $xQuantity The buy quantity.
     * @param int $yQuantity The get quantity.
     * 
     * @return void
     */
    private function checkItemQuantityForBxGy(int|string $id, int $xQuantity, int $yQuantity): void
    {
        $itemQuantity = $this->item($id)->toArray()['quantity'];
        if ($itemQuantity < ($xQuantity + $yQuantity)) {
            throw new Exception('Quantity does not satisfy BxGy buy and get quantities for item with Id: ' . $this->item($id)->toArray()['id']);
        }
    }

    /**
     * Validates item or cart before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param mixed $id Unique identifier for item based validations.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validate(string $for, mixed $id = null, int|float $upto = 0, int $xQuantity = 0, int $yQuantity = 0): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new Exception('Invalid validator for item validation');
        }

        switch ($for) {
            case 'addingItem':
                $this->checkHasCartInitiated($id, 'new item');
                $this->checkItemExist($id);
                break;
            case 'applyingFlatDiscountOnItem':
                $this->checkHasCartInitiated($id, 'discount');
                $this->checkItemDoesNotExist($id);
                break;
            case 'applyingPercentageDiscountOnItem':
                $this->checkHasCartInitiated($id, 'discount');
                $this->checkUptoAmount($id, $upto);
                $this->checkItemDoesNotExist($id);
                break;
            case 'applyingBxGyOnItem':
                $this->checkHasCartInitiated($id, 'discount');
                $this->checkItemDoesNotExist($id);
                $this->checkBxGyQuantity($id, $xQuantity, $yQuantity);
                $this->checkItemQuantityForBxGy($id, $xQuantity, $yQuantity);
                break;
            case 'applyingDeliveryChargeOnItem':
                $this->checkHasCartInitiated($id, 'delivery charge');
                $this->checkItemDoesNotExist($id);
                break;
            case 'applyingTaxOnItem':
                $this->checkHasCartInitiated($id, 'tax');
                $this->checkItemDoesNotExist($id);
                break;
            case 'applyingFlatDiscountOnCart':
                $this->checkIsCartEmpty();
                $this->checkItemLevelApplications('discount');
                $this->checkTaxHasBeenApplied();
                $this->checkDeliveryChargeHasBeenApplied();
                break;
            case 'applyingPercentageDiscountOnCart':
                $this->checkIsCartEmpty();
                $this->checkItemLevelApplications('discount');
                $this->checkTaxHasBeenApplied();
                $this->checkDeliveryChargeHasBeenApplied();
                $this->checkUptoAmountForCart($upto);
                break;
            case 'applyingDeliveryChargeOnCart':
                $this->checkIsCartEmpty();
                $this->checkItemLevelApplications('delivery charge');
                $this->checkDeliveryChargeBeforeAddingNew();
                break;
            case 'applyingTaxOnCart':
                $this->checkIsCartEmpty();
                $this->checkItemLevelApplications('tax');
                break;
            default:
                return;
        }
    }
}
