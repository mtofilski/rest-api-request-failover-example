<?php

declare(strict_types=1);

namespace App\CircuitBreaker;


use Ackintosh\Ganesha;
use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use App\CircuitBreaker\Extractor\URIExtractor;
use App\CircuitBreaker\Middleware\FailedTransportMiddleware;
use App\CircuitBreaker\Middleware\FailureDetectionMiddleware;
use App\CircuitBreaker\Transport\FailedTransport;

final class CircuitBreakerMiddleware
{
    public static function failedTransport(FailedTransport $transport): callable
    {
        return new FailedTransportMiddleware($transport);
    }

    public static function circuitBreaker(Ganesha $ganesha, ?ServiceNameExtractorInterface $extractor = null): callable
    {
        $serviceNameExtractor = $extractor ?? new URIExtractor();
        return new FailureDetectionMiddleware($ganesha, $serviceNameExtractor);
    }
}
