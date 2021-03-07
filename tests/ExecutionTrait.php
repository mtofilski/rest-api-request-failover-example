<?php

declare(strict_types=1);

namespace App\Tests;

trait ExecutionTrait
{
    public function executeTimes(int $numberOfCalls, callable $func): void
    {
        for ($i = 0; $i < $numberOfCalls; $i++) {
            $func();
        }
    }
}
