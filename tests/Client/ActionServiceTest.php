<?php

declare(strict_types=1);

namespace App\Tests\Client;


use App\Client\ActionService;
use App\Client\Request\FailureDetector\Storage\InMemoryFailedRequestStorage;
use App\Client\Request\Request;
use App\Tests\ExecutionTrait;
use PHPUnit\Framework\TestCase;

final class ActionServiceTest extends TestCase
{
    use ExecutionTrait;

    public function testShouldTriggerFailureDetection(): void
    {
        $storage = new InMemoryFailedRequestStorage();
        $request = new Request($storage);
        $service = new ActionService($request);

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('fail');
        });

        self::assertEquals(10, $storage->count());
    }

    public function testShouldPassWithoutErrors(): void
    {
        $storage = new InMemoryFailedRequestStorage();
        $request = new Request($storage);
        $service = new ActionService($request);

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('success');
        });

        self::assertEquals(0, $storage->count());
    }
}
