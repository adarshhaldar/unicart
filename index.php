<?php

use Unicart\Classes\Discount;
use Unicart\Classes\Item;
use Unicart\Unicart;

require 'vendor/autoload.php';
try {
   $cart = new Unicart();

   $cart->addItem(1, -123);

   print_r($cart->toArray());
} catch (Exception $e) {
    echo $e->getMessage();
}
