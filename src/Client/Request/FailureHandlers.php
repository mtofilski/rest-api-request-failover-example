<?php

declare(strict_types=1);

namespace App\Client\Request;

use App\Client\Request\Extractor\URIExtractor;
use App\Client\Request\FailureDetector\FailedRequestStorage;
use App\Client\Request\FailureDetector\GaneshaFailureDetector;
use App\Client\Request\Middleware\FailedRequestStorageMiddleware;
use App\Client\Request\Middleware\FailureDetectionMiddleware;
use GuzzleHttp\HandlerStack;

final class FailureHandlers
{
    private FailedRequestStorage $storage;
    private GaneshaFailureDetector $ganeshaFailureDetector;

    public function __construct(FailedRequestStorage $storage, GaneshaFailureDetector $ganeshaFailureDetector)
    {
        $this->storage = $storage;
        $this->ganeshaFailureDetector = $ganeshaFailureDetector;
    }

    public function getHandlers(): HandlerStack
    {
        $handlers = HandlerStack::create();
        $handlers->push(new FailureDetectionMiddleware($this->ganeshaFailureDetector->loadStrategy(), new URIExtractor()));
        $handlers->push(new FailedRequestStorageMiddleware($this->storage));

        return $handlers;
    }

}
