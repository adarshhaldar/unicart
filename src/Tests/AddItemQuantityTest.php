<?php

namespace Unicart\Tests;

use Exception;
use Unicart\Unicart;

class AddItemQuantityTest extends BaseTest
{
    private $cart = null;

    public function __construct()
    {
        $this->cart = new Unicart();
        $this->divider(self::class);
    }

    private function successAddWithIntQuantity()
    {
        try {
            $this->testDescription('Adding item with valid integer quantity 5.');
            $this->cart->addItem('item1', 100, 5);
            $this->success('Item added successfully with quantity: 5');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddWithStringNumberQuantity()
    {
        try {
            $this->testDescription('Adding item with stringified number quantity "2".');
            $this->cart->addItem('item2', 100, '2');
            $this->success('Item added successfully with quantity: "2"');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddWithFloatQuantity()
    {
        try {
            $this->testDescription('Trying to add item with float quantity 1.5. (will be coerced to "1")');
            $this->cart->addItem('item3', 100, 1.5);
            $this->success('Item added successfully with quantity: "1"');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function failureAddWithNegativeQuantity()
    {
        try {
            $this->testDescription('Trying to add item with negative quantity -3.');
            $this->cart->addItem('item4', 100, -3);
            $this->success('Unexpected: item added with negative quantity!');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function failureAddWithZeroQuantity()
    {
        try {
            $this->testDescription('Trying to add item with zero quantity.');
            $this->cart->addItem('item5', 100, 0);
            $this->success('Unexpected: item added with zero quantity!');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function run()
    {
        $this->successAddWithIntQuantity();
        $this->successAddWithStringNumberQuantity();
        $this->successAddWithFloatQuantity();

        $this->failureAddWithNegativeQuantity();
        $this->failureAddWithZeroQuantity();

        print_r($this->cart->toArray());
    }
}
