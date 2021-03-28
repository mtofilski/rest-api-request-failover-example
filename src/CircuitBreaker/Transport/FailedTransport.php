<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Transport;

use Psr\Http\Message\RequestInterface;

interface FailedTransport
{
    public const RETRY_HEADER = 'x-retry';

    public function store(RequestInterface $request): void;

    public function pop(): ?RequestInterface;

    public function count(): int;
}
