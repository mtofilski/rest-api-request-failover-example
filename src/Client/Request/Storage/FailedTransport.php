<?php

declare(strict_types=1);

namespace App\Client\Request\Storage;

use Psr\Http\Message\RequestInterface;

interface FailedTransport
{
    public function store(RequestInterface $request): void;

    public function pop(): ?RequestInterface;

    public function count(): int;
}
