<?php

namespace App\CircuitBreaker\Middleware;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Exception\RejectedException;
use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use Closure;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FailureDetectionMiddleware
{
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_SERVICE_UNAVAILABLE = 503;

    private Ganesha $ganesha;
    private ServiceNameExtractorInterface $serviceNameExtractor;

    public function __construct(
        Ganesha $ganesha,
        ServiceNameExtractorInterface $serviceNameExtractor
    ) {
        $this->ganesha = $ganesha;
        $this->serviceNameExtractor = $serviceNameExtractor;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $serviceName = $this->serviceNameExtractor->extract($request, $options);

            if (!$this->ganesha->isAvailable($serviceName)) {
                return Create::rejectionFor(
                    new RejectedException(
                        sprintf('"%s" is not available', $serviceName), self::HTTP_SERVICE_UNAVAILABLE
                    )
                );
            }

            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($serviceName) {
                    if ($response->getStatusCode() >= self::HTTP_BAD_REQUEST) {
                        $this->ganesha->failure($serviceName);
                    } else {
                        $this->ganesha->success($serviceName);
                    }
                    return Create::promiseFor($response);
                },
                function ($exception) use ($serviceName) {
                    $this->ganesha->failure($serviceName);
                    return Create::rejectionFor($exception);
                }
            );
        };
    }
}
