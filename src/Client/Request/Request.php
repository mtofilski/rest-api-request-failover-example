<?php

declare(strict_types=1);

namespace App\Client\Request;

use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\Middleware\GaneshaGuzzleMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Memcached;

final class Request
{
    public function getClient()
    {
        $mc = new Memcached('mc');
        $mc->addServers(array(
            array('localhost',11211),
        ));
        $ganesha  = new GaneshaFailureDetector(new \Ackintosh\Ganesha\Storage\Adapter\Memcached($mc));
        $middleware = new GaneshaGuzzleMiddleware($ganesha->loadStrategy(), new URIExtractor());
        $handlers = HandlerStack::create();
        $handlers->push($middleware);

        return new Client([
            'base_uri' => 'https://localhost:8000',
            'verify' => false,
            'handler' => $handlers
        ]);
    }

}
