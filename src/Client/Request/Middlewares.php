<?php

declare(strict_types=1);

namespace App\Client\Request;

use ArrayIterator;
use GuzzleHttp\HandlerStack;
use IteratorIterator;

final class Middlewares extends IteratorIterator
{
    public function __construct(Middleware ...$iterator)
    {
        parent::__construct(new ArrayIterator($iterator));
    }

    public function getAll(): HandlerStack
    {

        $handlers = HandlerStack::create();
        $this->rewind();
        while ($this->valid()) {
            $handlers->push($this->current());
            $this->next();
        }
        return $handlers;
    }
}
