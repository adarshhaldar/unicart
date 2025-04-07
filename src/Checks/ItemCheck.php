<?php

namespace Unicart\Checks;

use Exception;

trait ItemCheck
{
    private function checkTaxBeforeApplyingDiscount()
    {
        if (count($this->taxes) > 0) {
            throw new Exception('Can not add discount after taxation for item with Id: ' . $this->id);
        }
    }

    private function checkDeliveryChargeBeforeApplyingDiscount()
    {
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add discount after adding delivery charge for item with Id: ' . $this->id);
        }
    }

    private function checkDeliveryChargeBeforeAddingNew()
    {
        if (count($this->deliveryCharge) > 0) {
            throw new Exception('Can not add another delivery charge for item with Id: ' . $this->id);
        }
    }
}
