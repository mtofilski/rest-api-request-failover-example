<?php

declare(strict_types=1);

namespace App\Client\Request\FailureDetector\Storage;

use App\Client\Request\FailureDetector\FailedRequestStorage;
use Psr\Http\Message\RequestInterface;

final class InMemoryFailedRequestStorage implements FailedRequestStorage
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

    public function count(): int
    {
        return count($this->inMemoryRequests);
    }
}
