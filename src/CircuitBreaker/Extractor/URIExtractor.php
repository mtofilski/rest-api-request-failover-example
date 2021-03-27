<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Extractor;

use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use Psr\Http\Message\RequestInterface;

final class URIExtractor implements ServiceNameExtractorInterface
{
    public function extract(RequestInterface $request, array $requestOptions): string
    {
        return $request->getUri()->getHost() . $request->getUri()->getPath();
    }
}
