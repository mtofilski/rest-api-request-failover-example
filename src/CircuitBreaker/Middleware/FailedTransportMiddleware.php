<?php

namespace App\CircuitBreaker\Middleware;

use App\CircuitBreaker\Transport\FailedTransport;
use Closure;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FailedTransportMiddleware
{
    private FailedTransport $transport;

    public function __construct(
        FailedTransport $transport
    ) {
        $this->transport = $transport;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($request) {
                    if ($response->getStatusCode() >= 400) {
                        $this->transport->store($request);
                    }
                    return Create::promiseFor($response);
                },
                function ($exception) use ($request) {
                    $this->transport->store($request);
                    return Create::rejectionFor($exception);
                }
            );
        };
    }
}
