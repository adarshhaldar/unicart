<?php

namespace Unicart\Validators;

use Unicart\Exceptions\UnicartException;

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
        'applyingTaxOnCart',
        'applyingSpendXGetYOffOnCart'
    ];

    /**
     * Checks if cart is empty.
     * 
     * @return void
     */
    private function checkIsCartEmpty(): void
    {
        if (count($this->cartItems) === 0) {
            throw new UnicartException('Cart is empty');
        }
    }

    /**
     * Checks new item's id
     * 
     * @param int|string $id Unique identifier for item.
     * 
     * @return void
     */
    private function checkItemId(int|string $id): void
    {
        if (is_float($id)) {
            throw new UnicartException('Float values are not allowed as item IDs. Id: ' . $id);
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
    private function checkItemPrice(int|string $id, int|float $price): void
    {
        if ($price <= 0) {
            throw new UnicartException('Price for item with Id: ' . $id . ' can not be less than or equal to 0.');
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
    private function checkItemQuantity(int|string $id, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new UnicartException('Quantity for item with Id: ' . $id . ' can not be less than or equal to 0.');
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
            throw new UnicartException('Item with Id: ' . $id . ' already exist.');
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
            throw new UnicartException('Item with Id: ' . $id . ' does not exist.');
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
            throw new UnicartException('Upto discount amount for Item with Id: ' . $id . ' is invalid.');
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
                throw new UnicartException('Can not apply ' . $applying . ' on cart as tax has already been applied on item with Id: ' . $item['id']);
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
                throw new UnicartException('Can not apply ' . $applying . ' on cart as delivery charge has already been applied on item with Id: ' . $item['id']);
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
            throw new UnicartException('Can not add ' . $applying . ' after cart initiation on item with Id: ' . $id);
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
            throw new UnicartException('Upto discount amount for cart is invalid.');
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
            throw new UnicartException('Can not add discount after taxation');
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
            throw new UnicartException('Can not add discount after adding delivery charge');
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
            throw new UnicartException('Can not add another delivery charge on the cart');
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
            throw new UnicartException('Buy or get quantity cannot be less than or equal to 0 for item with Id: ' . $this->item($id)->toArray()['id']);
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
            throw new UnicartException('Item quantity is insufficient for the specified BxGy values for item with Id: ' . $this->item($id)->toArray()['id']);
        }
    }

    /**
     * Checks the validity of sxgy values
     * 
     * @param int|float $spend The cart expenditure.
     * @param int|float $get The off amount.
     * 
     * @return void
     */
    private function checkSpendXGetYValidity(int|float $spend, int|float $get): void
    {
        if ($this->isSxGyApplied) {
            throw new UnicartException('Can not apply another SxGy discount on the cart');
        }

        if ($spend <= 0 || $get <= 0) {
            throw new UnicartException('Spend or Get can not be less than or equal to 0 of spendXgetY discount');
        }

        if ($spend < $get) {
            throw new UnicartException('Spend can not be less than get for spendXgetY discount');
        }
    }

    /**
     * Checks the stacking of discount
     * 
     * @return void
     */
    private function checkDiscountStacking(): void
    {
        if (!$this->allowDiscountStacking && ($this->cartHasDiscount || $this->anyItemHasDiscount)) {
            throw new UnicartException('Discount stacking is disabled. A discount has already been applied.');
        }
    }

    /**
     * Checks the discount percentage
     * 
     * @param int|float $percentage The discount percentage.
     * @param mixed $id A unique identifier for item.
     * 
     * @return void
     */
    private function checkDiscountPercentage(int|float $percentage, mixed $id = null): void
    {
        if ($percentage <= 0) {
            $message = $id ? 'Discount percentage can not be less than or equal to 0 for item with Id: ' . $id : 'Discount percentage can not be less than or equal to 0';
            throw new UnicartException($message);
        }
    }

    /**
     * Checks the discount amount
     * 
     * @param int|float $discount The discount amount.
     * @param mixed $id A unique identifier for item.
     * 
     * @return void
     */
    private function checkDiscountAmount(int|float $discount, mixed $id = null): void
    {
        if ($discount <= 0) {
            $message = $id ? 'Discount can not be less than or equal to 0 for item with Id: ' . $id : 'Discount can not be less than or equal to 0';
            throw new UnicartException($message);
        }
    }

    /**
     * Checks the delivery charge
     * 
     * @param int|float $charge The delivery charge.
     * @param mixed $id A unique identifier for item.
     * 
     * @return void
     */
    private function checkDeliveryCharge(int|float $charge, mixed $id = null): void
    {
        if ($charge <= 0) {
            $message = $id ? 'Delivery charge can not be less than or equal to 0 for item with Id: ' . $id : 'Delivery charge can not be less than or equal to 0';
            throw new UnicartException($message);
        }
    }

    /**
     * Checks the tax rate
     * 
     * @param int|float $rate The tax rate in %.
     * @param mixed $id A unique identifier for item.
     * 
     * @return void
     */
    private function checkTaxRate(int|float $rate, mixed $id = null): void
    {
        if ($rate <= 0) {
            $message = $id ? 'Tax can not be less than or equal to 0 for item with Id: ' . $id : 'Tax can not be less than or equal to 0';
            throw new UnicartException($message);
        }
    }

    /**
     * Validates application of tax on cart
     * 
     * @param @rate The tax rate.
     * 
     * @return void
     */
    private function validateApplyingTaxOnCart(int|float $rate): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('tax');

        $this->checkTaxRate($rate);
    }

    /**
     * Validates application of delivery charge on cart
     * 
     * @param int|float $charge The delivery charge.
     * 
     * @return void
     */
    private function validateApplyingDeliveryChargeOnCart(int|float $charge): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('delivery charge');
        $this->checkDeliveryChargeBeforeAddingNew();

        $this->checkDeliveryCharge($charge);
    }

    /**
     * Validates application of sxgy on cart
     * 
     * @param int|float $spend The cart expenditure.
     * @param int|float $get The off amount.
     * 
     * @return void
     */
    private function validateSpendXGetYDiscountOnCart(int|float $spend, int|float $get): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();
        $this->checkSpendXGetYValidity($spend, $get);

        $this->checkDiscountStacking();
        if (!$this->cartHasDiscount) {
            $this->cartHasDiscount = true;
        }
    }

    /**
     * Validates application of percentage-based discount on cart
     * 
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscountOnCart(int|float $percentage, int|float $upto): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();
        $this->checkUptoAmountForCart($upto);

        $this->checkDiscountStacking();
        if (!$this->cartHasDiscount) {
            $this->cartHasDiscount = true;
        }

        $this->checkDiscountPercentage($percentage);
    }

    /**
     * Validates application of flat discount on cart
     * 
     * @param int|float $discount The discount amount.
     * 
     * @return void
     */
    private function validateApplyingFlatDiscountOnCart(int|float $discount): void
    {
        $this->checkIsCartEmpty();
        $this->checkItemLevelApplications('discount');
        $this->checkTaxHasBeenApplied();
        $this->checkDeliveryChargeHasBeenApplied();

        $this->checkDiscountStacking();
        if (!$this->cartHasDiscount) {
            $this->cartHasDiscount = true;
        }

        $this->checkDiscountAmount($discount);
    }

    /**
     * Validates application of tax on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $rate The tax rate in %.
     * 
     * @return void
     */
    private function validateApplyingTaxOnItem(int|string $id, int|float $rate): void
    {
        $this->checkHasCartInitiated($id, 'tax');
        $this->checkItemDoesNotExist($id);

        $this->checkTaxRate($rate, $id);
    }

    /**
     * Validates application of delivery charge on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $charge The delivery charge.
     * 
     * @return void
     */
    private function validateApplyingDeliveryChargeOnItem(int|string $id, int|float $charge): void
    {
        $this->checkHasCartInitiated($id, 'delivery charge');
        $this->checkItemDoesNotExist($id);

        $this->checkDeliveryCharge($charge, $id);
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

        $this->checkDiscountStacking();
        if (!$this->anyItemHasDiscount) {
            $this->anyItemHasDiscount = true;
        }
    }

    /**
     * Validates application of percentage-based discount on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $percentage The discount percentage.
     * @param int|float $upto The maximum discount allowed in percentage-based discounts. Defaults to 0 (no limit).
     * 
     * @return void
     */
    private function validateApplyingPercentageDiscountOnItem(int|string $id, int|float $percentage, int|float $upto): void
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkUptoAmount($id, $upto);
        $this->checkItemDoesNotExist($id);

        $this->checkDiscountStacking();
        if (!$this->anyItemHasDiscount) {
            $this->anyItemHasDiscount = true;
        }

        $this->checkDiscountPercentage($percentage, $id);
    }

    /**
     * Validates application of flat discount on item
     * 
     * @param int|string $id Unique identifier for item.
     * @param int|float $discount The discount amount.
     * 
     * @return void
     */
    private function validateApplyingFlatDiscountOnItem(int|string $id, int|float $discount): void
    {
        $this->checkHasCartInitiated($id, 'discount');
        $this->checkItemDoesNotExist($id);

        $this->checkDiscountStacking();
        if (!$this->anyItemHasDiscount) {
            $this->anyItemHasDiscount = true;
        }

        $this->checkDiscountAmount($discount, $id);
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
        $this->checkItemId($id);
        $this->checkItemPrice($id, $price);
        $this->checkItemQuantity($id, $quantity);
        $this->checkHasCartInitiated($id, 'new item');

        if (!$this->allowItemOverride) {
            $this->checkItemExist($id);
        }
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
     * Validates item or cart before mentioned operations/applications
     * 
     * @param string $for Validate for variable to check through available validations.
     * @param array $params List of parameters.
     * 
     * @return void
     */
    private function validate(string $for, $params = []): void
    {
        if (!in_array($for, self::VALIDATORS)) {
            throw new UnicartException('Invalid validator for validation');
        }

        list($id, $price, $quantity, $discount, $percentage, $upto, $charge, $rate, $xQuantity, $yQuantity, $spend, $get) = $this->getVariablesFromParams($params);

        match ($for) {
            'addingItem' => $this->validateAddingItem($id, $price, $quantity),
            'applyingFlatDiscountOnItem' => $this->validateApplyingFlatDiscountOnItem($id, $discount),
            'applyingPercentageDiscountOnItem' => $this->validateApplyingPercentageDiscountOnItem($id, $percentage, $upto),
            'applyingBxGyOnItem' => $this->validateApplyingBxGyOnItem($id, $xQuantity, $yQuantity),
            'applyingDeliveryChargeOnItem' => $this->validateApplyingDeliveryChargeOnItem($id, $charge),
            'applyingTaxOnItem' => $this->validateApplyingTaxOnItem($id, $rate),
            'applyingFlatDiscountOnCart' => $this->validateApplyingFlatDiscountOnCart($discount),
            'applyingPercentageDiscountOnCart' => $this->validateApplyingPercentageDiscountOnCart($percentage, $upto),
            'applyingDeliveryChargeOnCart' => $this->validateApplyingDeliveryChargeOnCart($charge),
            'applyingTaxOnCart' => $this->validateApplyingTaxOnCart($rate),
            'applyingSpendXGetYOffOnCart' => $this->validateSpendXGetYDiscountOnCart($spend, $get),
        };
    }
}
