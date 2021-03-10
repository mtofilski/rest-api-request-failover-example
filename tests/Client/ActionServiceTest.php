<?php

declare(strict_types=1);

namespace App\Tests\Client;


use App\Client\ActionService;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\FailureDetector\Storage\InMemoryRetryStorage;
use App\Client\Request\FailureHandlers;
use App\Tests\ExecutionTrait;
use GuzzleHttp\Client;
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
        $storage = new InMemoryRetryStorage();
        $failureDetection = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $request = new FailureHandlers($storage, $failureDetection);
        $client = new Client([
            'base_uri' => 'https://localhost:8000',
            'verify'   => false,
            'handler'  => $request->getHandlers()
        ]);
        $service = new ActionService($client);

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
        $storage = new InMemoryRetryStorage();
        $failureDetection = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $request = new FailureHandlers($storage, $failureDetection);
        $client = new Client([
            'base_uri' => 'https://localhost:8000',
            'verify'   => false,
            'handler'  => $request->getHandlers()
        ]);
        $service = new ActionService($client);

        $this->executeTimes(10, function() use ($service) {
            $service->makeSomeAction('success');
        });

        self::assertEquals(0, $storage->count());
    }
}
