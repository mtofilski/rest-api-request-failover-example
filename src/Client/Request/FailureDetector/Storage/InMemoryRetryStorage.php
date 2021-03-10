<?php

declare(strict_types=1);

namespace App\Client\Request\FailureDetector\Storage;

use App\Client\Request\Storage\RetryStorage;
use Psr\Http\Message\RequestInterface;

final class InMemoryRetryStorage implements RetryStorage
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
