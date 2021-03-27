<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use Ackintosh\Ganesha\Builder;
use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\Middleware\FailedTransportMiddleware;
use App\Client\Request\Middleware\FailureDetectionMiddleware;
use App\Client\Request\Storage\FailedTransport;
use Memcached;

final class MiddlewareFixtures
{
    public static function aFailureDetectionMiddleware(): FailureDetectionMiddleware
    {
        $mc = new Memcached('mc');
        $mc->addServers(
            [
                ['localhost', 11211],
            ]
        );
        $mc->flush();

        $ganesha = Builder::withRateStrategy()
            ->failureRateThreshold(50)
            ->intervalToHalfOpen(10)
            ->minimumRequests(2)
            ->timeWindow(30)
            ->adapter(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc))
            ->build();

        return new FailureDetectionMiddleware($ganesha, new URIExtractor());
    }

    public static function aRetryStorageMiddleware(FailedTransport $storage): FailedTransportMiddleware
    {
        return new FailedTransportMiddleware($storage);
    }

}
