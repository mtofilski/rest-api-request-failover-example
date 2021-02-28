<?php

declare(strict_types=1);

namespace App\Client\Request\Repository;

use Psr\Http\Message\RequestInterface;

final class InMemoryFailedRequestRepository implements FailedRequestRepository
{
    private array $inMemoryRequests = [];


    public function add(RequestInterface $request): void
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
}
