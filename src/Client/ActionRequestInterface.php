<?php

declare(strict_types=1);

namespace App\Client;

interface ActionRequestInterface
{
    public function request(string $actionType): int;
}
