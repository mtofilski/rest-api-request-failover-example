<?php

declare(strict_types=1);

namespace App\Client\Request\FailureDetector;

use Psr\Http\Message\RequestInterface;

interface FailedRequestStorage
{
    public function add(RequestInterface $request): void;

    public function pop(): ?RequestInterface;
}
