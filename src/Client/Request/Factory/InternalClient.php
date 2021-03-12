<?php

declare(strict_types=1);

namespace App\Client\Request\Factory;

use App\Client\Request\Middlewares;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

final class InternalClient
{
    private Middlewares $middlewares;

    public function __construct(Middlewares $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function __invoke(): ClientInterface
    {
        return new Client([
            'base_uri' => 'https://localhost:8000',
            'verify'   => false,
            'handler'  => $this->middlewares->getAll()
        ]);
    }

}
