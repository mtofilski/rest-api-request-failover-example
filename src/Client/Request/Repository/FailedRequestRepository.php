<?php

declare(strict_types=1);

namespace App\Client\Request\Repository;

use Psr\Http\Message\RequestInterface;

interface FailedRequestRepository
{
    public function add(RequestInterface $request): void;

    public function pop(): ?RequestInterface;
}
