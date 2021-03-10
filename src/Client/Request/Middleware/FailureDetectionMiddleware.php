<?php

namespace App\Client\Request\Middleware;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Exception\RejectedException;
use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use GuzzleHttp\Promise\Create;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FailureDetectionMiddleware
{
    private Ganesha $ganesha;
    private ServiceNameExtractorInterface $serviceNameExtractor;

    public function __construct(
        Ganesha $ganesha,
        ServiceNameExtractorInterface $serviceNameExtractor
    ) {
        $this->ganesha = $ganesha;
        $this->serviceNameExtractor = $serviceNameExtractor;
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $serviceName = $this->serviceNameExtractor->extract($request, $options);

            if (!$this->ganesha->isAvailable($serviceName)) {
                return Create::rejectionFor(
                    new RejectedException(
                        sprintf('"%s" is not available', $serviceName)
                    )
                );
            }

            $promise = $handler($request, $options);
            return $promise->then(
                function (ResponseInterface $response) use ($serviceName) {
                    if ($response->getStatusCode() >= 400) {
                        $this->ganesha->failure($serviceName);
                    } else {
                        $this->ganesha->success($serviceName);
                    }
                    return Create::promiseFor($response);
                },
                function (NetworkExceptionInterface $exception) use ($serviceName) {
                    $this->ganesha->failure($serviceName);
                    return Create::rejectionFor($exception);
                }
            );
        };
    }
}
