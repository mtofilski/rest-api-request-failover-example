<?php

declare(strict_types=1);

namespace App\Client\Request\Factory;

use App\Client\Request\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

final class InternalClient
{
    private array $middlewares;

    public function __construct(Middleware ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function __invoke(): ClientInterface
    {
        $handlers = HandlerStack::create();
        foreach($this->middlewares as $middleware)
        {
            $handlers->push($middleware);
        }
        return new Client([
            'base_uri' => 'https://localhost:8000',
            'verify'   => false,
            'handler'  => $handlers
        ]);
    }

}
