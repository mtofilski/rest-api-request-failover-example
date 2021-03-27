<?php

declare(strict_types=1);

namespace App\Client\Request\Factory;

use Ackintosh\Ganesha\Builder;
use Ackintosh\Ganesha\Storage\AdapterInterface;
use App\CircuitBreaker\CircuitBreakerMiddleware;
use App\CircuitBreaker\Transport\FailedTransport;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

final class InternalClient
{
    private FailedTransport $transport;
    private AdapterInterface $adapter;

    public function __construct(FailedTransport $transport, AdapterInterface $adapter)
    {
        $this->transport = $transport;
        $this->adapter = $adapter;
    }

    public function __invoke(): ClientInterface
    {
        $ganesha = Builder::withRateStrategy()
            ->failureRateThreshold(50)
            ->intervalToHalfOpen(10)
            ->minimumRequests(10)
            ->timeWindow(30)
            ->adapter($this->adapter)
            ->build();

        $handleStack = HandlerStack::create();
        $handleStack->push(CircuitBreakerMiddleware::failedTransport($this->transport));
        $handleStack->push(CircuitBreakerMiddleware::circuitBreaker($ganesha));
        return new Client(
            [
                'base_uri' => 'https://localhost:8000',
                'verify'   => false,
                'handler'  => $handleStack
            ]
        );
    }

}
