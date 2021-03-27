<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Transport;

use Psr\Http\Message\RequestInterface;

final class InMemoryFailedTransport implements FailedTransport
{
    private array $inMemoryRequests = [];


    public function store(RequestInterface $request): void
    {
        $this->inMemoryRequests[] = $request;
    }

    public function pop(): ?RequestInterface
    {
        if(empty($this->inMemoryRequests)) {
            return null;
        }
        return array_pop($this->inMemoryRequests);
    }

    public function count(): int
    {
        return count($this->inMemoryRequests);
    }
}
