<?php

namespace Unicart\Tests;

use Exception;
use Unicart\Unicart;

class AddItemIdTest extends BaseTest
{
    private $cart = null;

    public function __construct()
    {
        $this->cart = new Unicart();
        $this->divider(self::class);
    }

    private function failureAddFloatId()
    {
        try {
            $this->testDescription('Attempting to add item with float ID 1.25 (will be coerced to "1" and may conflict)');
            $this->cart->addItem(1.25, 999);
            $this->success('Item added with ID: "1" (from float 1.25)');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function failureAddStringId()
    {
        try {
            $this->testDescription('Adding item with string ID "1", expecting collision if "1" already exists from a float/int.');
            $this->cart->addItem('1', 999);
            $this->success('Item added with ID: "1" (string)');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddFloatStringId()
    {
        try {
            $this->testDescription('Adding item with stringified float ID "1.25", should succeed as unique string.');
            $this->cart->addItem('1.25', 999);
            $this->success('Item successfully added with ID: "1.25" (string)');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddStringId()
    {
        try {
            $this->testDescription('Adding item with alphanumeric string ID "Item1", should succeed.');
            $this->cart->addItem('Item1', 999);
            $this->success('Item successfully added with ID: "Item1"');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function successAddIntId()
    {
        try {
            $this->testDescription('Adding item with integer ID 1, should succeed.');
            $this->cart->addItem(1, 999);
            $this->success('Item successfully added with ID: "1" (int → string)');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function run()
    {
        $this->successAddIntId();
        $this->successAddStringId();
        $this->successAddFloatStringId();

        $this->failureAddStringId();
        $this->failureAddFloatId();

        print_r($this->cart->toArray());
    }
}
