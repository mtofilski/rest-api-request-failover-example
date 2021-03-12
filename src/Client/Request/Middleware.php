<?php

declare(strict_types=1);

namespace App\Client\Request;

interface Middleware
{
    public function __invoke(callable $handler): \Closure;
}
