<?php

declare(strict_types=1);

namespace App\Client;


use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Builder;
use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\Middleware\GaneshaGuzzleMiddleware;
use App\Client\Request\Repository\FailedRequestRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Memcached;

final class ActionService
{
    private Client $client;
    private FailedRequestRepository $repository;
    private Ganesha $ganesha;

    public function __construct(FailedRequestRepository $repository)
    {
        $mc = new Memcached('mc');
        $mc->addServers(array(
            array('localhost',11211),
        ));

        $adapter = new Ganesha\Storage\Adapter\Memcached($mc);
        $this->ganesha = Builder::withRateStrategy()
            ->failureRateThreshold(50)
            ->intervalToHalfOpen(10)
            ->minimumRequests(10)
            ->timeWindow(30)
            ->adapter($adapter)
            ->build();

        $middleware = new GaneshaGuzzleMiddleware($this->ganesha, new URIExtractor());

        $handlers = HandlerStack::create();
        $handlers->push($middleware);
        $this->client = new Client([
            'base_uri' => 'https://localhost:8000',
            'verify' => false,
            'handler' => $handlers
        ]);
        $this->repository = $repository;
    }


    public function makeSomeAction(string $actionType): int
    {
        // do stuff
        try {
            $response = $this->client->post($actionType, [
                'body' => '{"test":"OK"}'
            ]);
        } catch (RequestException $e) {
            $this->repository->add($e->getRequest());
            return $e->getCode();
        }
        return $response->getStatusCode();
    }


}
