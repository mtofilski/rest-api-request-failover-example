<?php

declare(strict_types=1);

namespace App\Client\Request\Factory;


use Ackintosh\Ganesha;
use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\Middleware\FailedTransportMiddleware;
use App\Client\Request\Middleware\FailureDetectionMiddleware;
use App\Client\Request\Storage\FailedTransport;

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
