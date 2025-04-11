<?php

use Unicart\Unicart;

require 'vendor/autoload.php';

try {
    $cart = new Unicart();
   
    $cart->addItem(301, 1000, 2)->applyBxGyOnItem(301, 1, 1)
    ->addItem(302, 800, 3)->applyPercentageDiscountOnItem(302, 10)
    ->addItem(303, 1200)->applyFlatDiscountOnItem(303, 200)
    ->addItem(304, 500, 2)
    ->applySpendxGetyOffOnCart(5000, 500);
    print_r($cart->toArray());
} catch (Exception $e) {
    echo $e->getMessage();
}
