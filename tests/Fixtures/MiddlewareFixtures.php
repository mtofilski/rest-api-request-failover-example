<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\Middleware\FailureDetectionMiddleware;
use App\Client\Request\Middleware\RetryStorageMiddleware;
use App\Client\Request\Storage\RetryStorage;
use Memcached;

final class MiddlewareFixtures
{
    public static function aFailureDetectionMiddleware(): FailureDetectionMiddleware
    {
        $mc = new Memcached('mc');
        $mc->addServers([
            ['localhost', 11211],
        ]);
        $mc->flush();
        $ganesha = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        return new FailureDetectionMiddleware($ganesha->__invoke(), new URIExtractor());
    }

    public static function aRetryStorageMiddleware(RetryStorage $storage): RetryStorageMiddleware
    {
        return new RetryStorageMiddleware($storage);
    }

}
