<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Client\Request\Storage\FailedTransport;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

final class ClientFixture
{
    public static function guzzleClient(FailedTransport $transport): ClientInterface
    {
        return new Client(
            [
                'base_uri' => 'https://localhost:8000',
                'verify'   => false,
                'handler'  => self::loadHandleStack($transport)
            ]
        );
    }

    public static function unverifiedClient(FailedTransport $transport): ClientInterface
    {
        return new Client(
            [
                'base_uri' => 'https://localhost:8000',
                'verify'   => true,
                'handler'  => self::loadHandleStack($transport)
            ]
        );
    }

    public static function wrongHostClient(FailedTransport $transport): ClientInterface
    {
        return new Client(
            [
                'base_uri' => 'https://localhost:8001',
                'verify'   => false,
                'handler'  => self::loadHandleStack($transport)
            ]
        );
    }

    private static function loadHandleStack(FailedTransport $transport): HandlerStack
    {
        $handleStack = HandlerStack::create();
        $handleStack->push(MiddlewareFixtures::aRetryStorageMiddleware($transport));
        $handleStack->push(MiddlewareFixtures::aFailureDetectionMiddleware());

        return $handleStack;
    }
}
