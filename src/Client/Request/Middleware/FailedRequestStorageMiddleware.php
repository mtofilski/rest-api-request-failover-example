<?php

namespace App\Client\Request\Middleware;

use App\Client\Request\FailureDetector\FailedRequestStorage;
use GuzzleHttp\Promise\Create;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FailedRequestStorageMiddleware
{
    private FailedRequestStorage $storage;

    public function __construct(
        FailedRequestStorage $storage
    ) {
        $this->storage = $storage;
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $promise = $handler($request, $options);
            return $promise->then(
                function (ResponseInterface $response) use ($request) {
                    if ($response->getStatusCode() === 500) {
                        $this->storage->add($request);
                    }
                    return Create::promiseFor($response);
                },
                function (NetworkExceptionInterface $exception) {
                    $this->storage->add($exception->getRequest());
                    return Create::rejectionFor($exception);
                }
            );
        };
    }
}
