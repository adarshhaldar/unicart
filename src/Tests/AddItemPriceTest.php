<?php

namespace Unicart\Tests;

use Exception;
use Unicart\Unicart;

class AddItemPriceTest extends BaseTest
{
    private $cart = null;

    public function __construct()
    {
        $this->cart = new Unicart();
        $this->divider(self::class);
    }

    private function successAddItemWithIntPrice()
    {
        try {
            $this->testDescription('Adding item with valid integer price 100.');
            $this->cart->addItem('item1', 100);
            $this->success('Item added successfully with price: 100');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddItemWithFloatPrice()
    {
        try {
            $this->testDescription('Adding item with valid float price 99.99.');
            $this->cart->addItem('item2', 99.99);
            $this->success('Item added successfully with price: 99.99');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddItemWithStringNumberPrice()
    {
        try {
            $this->testDescription('Adding item with stringified number price "45.50".');
            $this->cart->addItem('item3', '45.50');
            $this->success('Item added successfully with price: "45.50"');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function failureAddItemWithNegativePrice()
    {
        try {
            $this->testDescription('Trying to add item with negative price -20.');
            $this->cart->addItem('item4', -20);
            $this->success('Unexpected: item added with negative price!');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function failureAddItemWithZeroPrice()
    {
        try {
            $this->testDescription('Trying to add item with zero price 0.');
            $this->cart->addItem('item5', 0);
            $this->success('Unexpected: item added with zero price!');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function run()
    {
        $this->successAddItemWithIntPrice();
        $this->successAddItemWithFloatPrice();
        $this->successAddItemWithStringNumberPrice();

        $this->failureAddItemWithNegativePrice();
        $this->failureAddItemWithZeroPrice();

        print_r($this->cart->toArray());
    }
}
