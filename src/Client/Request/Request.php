<?php

declare(strict_types=1);

namespace App\Client\Request;

use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\FailureDetector\FailedRequestStorage;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\Middleware\FailedRequestStorageMiddleware;
use App\Client\Request\Middleware\FailureDetectionMiddleware;
use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Memcached;

final class Request
{
    private FailedRequestStorage $storage;

    public function __construct(FailedRequestStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getClient(): Client
    {
        $mc = new Memcached('mc');
        $mc->addServers(array(
            array('localhost',11211),
        ));
        $ganesha  = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $failureDetection = new FailureDetectionMiddleware($ganesha->loadStrategy(), new URIExtractor());
        $failedRequestStorage = new FailedRequestStorageMiddleware($this->storage);
        $handlers = HandlerStack::create();
        $handlers->push(Middleware::httpErrors(new BodySummarizer()));
        $handlers->push($failureDetection);
        $handlers->push($failedRequestStorage);



        return new Client([
            'base_uri' => 'https://localhost:8001',
            'verify' => false,
            'handler' => $handlers
        ]);
    }

}
