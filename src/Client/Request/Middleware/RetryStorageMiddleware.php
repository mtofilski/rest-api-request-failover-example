<?php

namespace App\Client\Request\Middleware;

use App\Client\Request\Middleware;
use App\Client\Request\Storage\RetryStorage;
use GuzzleHttp\Promise\Create;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RetryStorageMiddleware implements Middleware
{
    private RetryStorage $storage;

    public function __construct(
        RetryStorage $storage
    ) {
        $this->storage = $storage;
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($request) {
                    if ($response->getStatusCode() >= 400) {
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
