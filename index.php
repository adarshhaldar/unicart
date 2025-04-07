<?php

use Unicart\Classes\Item;
use Unicart\Unicart;

require 'vendor/autoload.php';
try {
    $cart = new Unicart();
    $cart->addItem(1, 1399, 2)
    ->addItem(2, 2499)
    ->addItem(3, 499, 3)->addPercentageDiscountOnCart(10);
    print_r($cart->summary());
} catch (Exception $e) {
    echo $e->getMessage();
}
