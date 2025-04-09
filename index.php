<?php

use Unicart\Classes\Item;
use Unicart\Unicart;

require 'vendor/autoload.php';
try {
    $cart = new Unicart();
    $cart->addItem(1, 123, 10)->applyBxGyOnItem(1, 4, 2, 'BoGo');
    print_r($cart->toArray());
} catch (Exception $e) {
    echo $e->getMessage();
}
