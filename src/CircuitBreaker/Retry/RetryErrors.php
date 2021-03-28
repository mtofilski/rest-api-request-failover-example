<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Retry;

use ArrayIterator;
use IteratorIterator;

final class RetryErrors extends IteratorIterator
{
    public function __construct(RetryError ...$errors)
    {
        parent::__construct(new ArrayIterator($errors));
    }

    public function current(): RetryError
    {
        return $this->getInnerIterator()->current();
    }

    public function add(RetryError $error): void
    {
        $this->getInnerIterator()->append($error);
    }
}
