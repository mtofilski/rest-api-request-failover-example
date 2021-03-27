<?php

declare(strict_types=1);

namespace App\Tests\Client;


use App\CircuitBreaker\Transport\InMemoryFailedTransport;
use App\Client\ActionService;
use App\Client\Infrastructure\HttpActionRequest;
use App\Tests\Client\Fixtures\ClientFixture;
use App\Tests\ExecutionTrait;
use PHPUnit\Framework\TestCase;

use function extension_loaded;

final class ActionServiceTest extends TestCase
{
    use ExecutionTrait;

    protected function setUp(): void
    {
        if (!extension_loaded('memcached')) {
            self::markTestSkipped('ext-memcached not found.');
        }
    }

    public function testShouldTriggerFailureDetection(): void
    {
        $storage = new InMemoryFailedTransport();
        $requestAdapter = new HttpActionRequest(ClientFixture::guzzleClient($storage));
        $service = new ActionService($requestAdapter);

        $this->executeTimes(
            10,
            function () use ($service) {
                $service->makeSomeAction('fail');
            }
        );

        self::assertEquals(10, $storage->count());
    }

    public function testShouldPassWithoutErrors(): void
    {
        $storage = new InMemoryFailedTransport();
        $requestAdapter = new HttpActionRequest(ClientFixture::guzzleClient($storage));
        $service = new ActionService($requestAdapter);

        $this->executeTimes(
            10,
            function () use ($service) {
                $service->makeSomeAction('success');
            }
        );

        self::assertEquals(0, $storage->count());
    }

    public function testShouldTriggerFailureDetectionWhenUnverifiedCert(): void
    {
        $storage = new InMemoryFailedTransport();
        $requestAdapter = new HttpActionRequest(ClientFixture::unverifiedClient($storage));
        $service = new ActionService($requestAdapter);

        $this->executeTimes(
            10,
            function () use ($service) {
                $service->makeSomeAction('success');
            }
        );

        self::assertEquals(10, $storage->count());
    }

    public function testShouldTriggerFailureDetectionWhenRequestWrongRoute(): void
    {
        $storage = new InMemoryFailedTransport();
        $requestAdapter = new HttpActionRequest(ClientFixture::unverifiedClient($storage));
        $service = new ActionService($requestAdapter);

        $this->executeTimes(
            10,
            function () use ($service) {
                $service->makeSomeAction('unknown');
            }
        );

        self::assertEquals(10, $storage->count());
    }

    public function testShouldTriggerFailureDetectionWhenRequestWrongHost(): void
    {
        $storage = new InMemoryFailedTransport();
        $requestAdapter = new HttpActionRequest(ClientFixture::wrongHostClient($storage));
        $service = new ActionService($requestAdapter);

        $this->executeTimes(
            10,
            function () use ($service) {
                $service->makeSomeAction('success');
            }
        );

        self::assertEquals(10, $storage->count());
    }
}
