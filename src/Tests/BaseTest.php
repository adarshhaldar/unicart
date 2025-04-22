<?php

namespace Unicart\Tests;

class BaseTest
{
    protected function divider(string $label)
    {
        echo "\n--- {$label} ---\n";
    }

    protected function error(string $error)
    {
        echo "\n\033[1;31mERROR: \033[0m{$error}\n";
    }

    protected function success(string $success)
    {
        echo "\n\033[1;32mSUCCESS: \033[0m{$success}\n";
    }

    protected function testDescription(string $title)
    {
        echo "\n\033[1;35m{$title}\033[0m";
    }
}
