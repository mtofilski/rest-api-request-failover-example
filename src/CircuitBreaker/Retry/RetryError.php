<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Retry;

use Psr\Http\Message\RequestInterface;

final class RetryError
{
    private RequestInterface $request;
    private string $reason;

    public function __construct(RequestInterface $request, string $reason)
    {
        $this->request = $request;
        $this->reason = $reason;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

}
