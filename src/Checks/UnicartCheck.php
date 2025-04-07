<?php

namespace Unicart\Checks;

use Exception;

trait UnicartCheck
{
    private function checkItemExist(int|string $id)
    {
        if (isset($this->cartItems[$id])) {
            throw new Exception('Item with Id: ' . $id . ' already exist.');
        }
    }

    private function checkItemDoesNotExist(int|string $id)
    {
        if (!isset($this->cartItems[$id])) {
            throw new Exception('Item with Id: ' . $id . ' does not exist.');
        }
    }

    private function checkUptoAmount(int|string $id, int|float $upto)
    {
        if ($upto < 0) {
            throw new Exception('Upto discount amount for Item with Id: ' . $id . ' is invalid.');
        }
    }

    private function checkAnyItemHasTaxApplied(string $applying)
    {
        foreach ($this->items() as $item) {
            if ($item['taxes'] != null) {
                throw new Exception('Can not apply ' . $applying . ' on cart as tax has already been applied on item with Id: ' . $item['id']);
            }
        }
    }

    private function checkAnyItemHasDeliveryChargeApplied(string $applying)
    {
        foreach ($this->items() as $item) {
            if ($item['deliveryCharge'] != null) {
                throw new Exception('Can not apply ' . $applying . ' on cart as delivery charge has already been applied on item with Id: ' . $item['id']);
            }
        }
    }

    private function checkItemLevelApplications(string $applying)
    {
        $this->checkAnyItemHasTaxApplied($applying);
        $this->checkAnyItemHasDeliveryChargeApplied($applying);

        if (!$this->hasCartApplicationInitiated) {
            $this->hasCartApplicationInitiated = true;
        }
    }

    private function checkHasCartInitiated(int|string $id, string $applying)
    {
        if ($this->hasCartApplicationInitiated) {
            throw new Exception('Can not add ' . $applying . ' after cart initiation on item with Id: ' . $id);
        }
    }

    private function checkUptoAmountForCart(int|float $upto)
    {
        if ($upto < 0) {
            throw new Exception('Upto discount amount for cart is invalid.');
        }
    }

    private function checkTaxHasBeenApplied()
    {
        if (count($this->taxes) > 0) {
            throw new Exception('Can not add discount after taxation');
        }
    }

    private function checkDeliveryChargeHasBeenApplied()
    {
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add discount after adding delivery charge');
        }
    }

    private function checkDeliveryChargeBeforeAddingNew(){
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add another delivery charge on the cart');
        }
    }
}
