<?php

namespace Unicart\Validators;

use Exception;

trait UnicartValidator
{
    /**
     * Validators for item and cart level validation before any application
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
        if (count($this->cartItems) === 0) {
            throw new Exception('Cart is empty');
        }
    }

    /**
     * Validates addition of new item's price
     * 
     * @param mixed $id Unique identifier for item.
     * @param int|float $price Price of the item.
     * 
     * @return void
     */
    private function checkItemPrice(int|string $id, int|float $price): void
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
    private function checkItemQuantity(int|string $id, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity for item with Id: ' . $id . ' can not be less than or equal to 0.');
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
        if ($xQuantity <= 0 || $yQuantity <= 0) {
            throw new Exception('Buy or get quantity cannot be less than or equal to 0 for item with Id: ' . $this->item($id)->toArray()['id']);
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
            throw new Exception('Item quantity is insufficient for the specified BxGy values for item with Id: ' . $this->item($id)->toArray()['id']);
        }
    }

    /**
     * Validates application of tax on cart
     * 
     * @return void
     */
    private function validateApplyingTaxOnCart(): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('tax');
    }

    /**
     * Validates application of delivery charge on cart
     * 
     * @return void
     */
    private function validateApplyingDeliveryChargeOnCart(): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('delivery charge');
        $this->checkDeliveryChargeBeforeAddingNew();
    }

    /**
     * Validates application of percentage-based discount on cart
     * 
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscountOnCart(int|float $upto): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();
        $this->checkUptoAmountForCart($upto);
    }

    /**
     * Validates application of flat discount on cart
     * 
     * @return void
     */
    private function validateApplyingFlatDiscountOnCart(): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();
    }

    /**
     * Validates application of tax on item
     * 
     * @param int|string $id Unique identifier for item.
     * 
     * @return void
     */
    private function validateApplyingTaxOnItem(int|string $id): void
    {
        $this->checkHasCartInitiated($id, 'tax');
        $this->checkItemDoesNotExist($id);
    }

    /**
     * Validates application of delivery charge on item
     * 
     * @param int|string $id Unique identifier for item.
     * 
     * @return void
     */
    private function validateApplyingDeliveryChargeOnItem(int|string $id): void
    {
        $this->checkHasCartInitiated($id, 'delivery charge');
        $this->checkItemDoesNotExist($id);
    }

    /**
     * Validates application of BxGy discount on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int $xQuantity The buy quantity. Defaults to 0.
     * @param int $yQuantity The get quantity. Defaults to 0.
     * 
     * @return void
     */
    private function validateApplyingBxGyOnItem(int|string $id, int $xQuantity, int $yQuantity): void
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkItemDoesNotExist($id);
        $this->checkBxGyQuantity($id, $xQuantity, $yQuantity);
        $this->checkItemQuantityForBxGy($id, $xQuantity, $yQuantity);
    }

    /**
     * Validates application of percentage-based discount on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscountOnItem(int|string $id, int|float $upto): void
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkUptoAmount($id, $upto);
        $this->checkItemDoesNotExist($id);
    }

    /**
     * Validates application of flat discount on item
     * 
     * @param int|string $id Unique identifier for item.
     * 
     * @return void
     */
    private function validateApplyingFlatDiscountOnItem(int|string $id): void
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkItemDoesNotExist($id);
    }

    /**
     * Validates item creation
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $price Price of the item.
     * @param int $quantity Quantity of the item.
     * 
     * @return void
     */
    private function validateAddingItem(int|string $id, int|float $price, int $quantity): void
    {
        $this->checkItemPrice($id, $price);
        $this->checkItemQuantity($id, $quantity);
        $this->checkHasCartInitiated($id, 'new item');
        $this->checkItemExist($id);
    }

    /**
     * Validates item or cart before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param mixed $id Unique identifier for item based validations.
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
            'applyingFlatDiscountOnItem' => $this->validateApplyingFlatDiscountOnItem($id),
            'applyingPercentageDiscountOnItem' => $this->validateApplyingPercentageDiscountOnItem($id, $upto),
            'applyingBxGyOnItem' => $this->validateApplyingBxGyOnItem($id, $xQuantity, $yQuantity),
            'applyingDeliveryChargeOnItem' => $this->validateApplyingDeliveryChargeOnItem($id),
            'applyingTaxOnItem' => $this->validateApplyingTaxOnItem($id),
            'applyingFlatDiscountOnCart' => $this->validateApplyingFlatDiscountOnCart(),
            'applyingPercentageDiscountOnCart' => $this->validateApplyingPercentageDiscountOnCart($upto),
            'applyingDeliveryChargeOnCart' => $this->validateApplyingDeliveryChargeOnCart(),
            'applyingTaxOnCart' => $this->validateApplyingTaxOnCart()
        };
    }
}
