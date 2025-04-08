<?php

use Unicart\Classes\Item;
use Unicart\Unicart;

require 'vendor/autoload.php';
try {
    $cart = new Unicart();
    $cart->addItem(1, 123)->applyTaxOnItem(1, 10);
    print_r($cart->item(1)->taxes());
    // print_r($cart->toArray());
} catch (Exception $e) {
    echo $e->getMessage();
}
