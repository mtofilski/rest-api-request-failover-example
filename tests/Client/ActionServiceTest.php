<?php

declare(strict_types=1);

namespace App\Tests\Client;


use App\Client\ActionService;
use App\Client\Request\Factory\InternalClient;
use App\Client\Request\FailureDetector\Storage\InMemoryRetryStorage;
use App\Client\Request\Middlewares;
use App\Tests\ExecutionTrait;
use App\Tests\Fixtures\MiddlewareFixtures;
use PHPUnit\Framework\TestCase;

final class ActionServiceTest extends TestCase
{
    use ExecutionTrait;

    public function testShouldTriggerFailureDetection(): void
    {
        $storage = new InMemoryRetryStorage();
        $client = new InternalClient(new Middlewares(
                MiddlewareFixtures::aFailureDetectionMiddleware(),
                MiddlewareFixtures::aRetryStorageMiddleware($storage)
            )
        );
        $service = new ActionService($client->__invoke());

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('fail');
        });

        self::assertEquals(10, $storage->count());
    }

    public function testShouldPassWithoutErrors(): void
    {
        $storage = new InMemoryRetryStorage();
        $client = new InternalClient(new Middlewares(
                MiddlewareFixtures::aFailureDetectionMiddleware(),
                MiddlewareFixtures::aRetryStorageMiddleware($storage)
            )
        );
        $service = new ActionService($client->__invoke());

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('success');
        });

        self::assertEquals(0, $storage->count());
    }
}
