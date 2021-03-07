<?php

declare(strict_types=1);

namespace App\Tests\Client;


use App\Client\ActionService;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\FailureDetector\Storage\InMemoryFailedRequestStorage;
use App\Client\Request\FailureHandlers;
use App\Tests\ExecutionTrait;
use Memcached;
use PHPUnit\Framework\TestCase;

final class ActionServiceTest extends TestCase
{
    use ExecutionTrait;

    public function testShouldTriggerFailureDetection(): void
    {
        $mc = new Memcached('mc');
        $mc->addServers(array(
            array('localhost',11211),
        ));
        $storage = new InMemoryFailedRequestStorage();
        $failureDetection = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $request = new FailureHandlers($storage, $failureDetection);
        $service = new ActionService($request);

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('fail');
        });

        self::assertEquals(10, $storage->count());
    }

    public function testShouldPassWithoutErrors(): void
    {
        $mc = new Memcached('mc');
        $mc->addServers(array(
            array('localhost',11211),
        ));
        $storage = new InMemoryFailedRequestStorage();
        $failureDetection = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $request = new FailureHandlers($storage, $failureDetection);
        $service = new ActionService($request);

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('success');
        });

        self::assertEquals(0, $storage->count());
    }
}
