<?php

declare(strict_types=1);

namespace App\Client\Request\FailureDetector;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Builder;
use Ackintosh\Ganesha\Storage\AdapterInterface;

final class GaneshaFailureDetector
{
    private AdapterInterface $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }


    public function loadStrategy(): Ganesha
    {
        return Builder::withRateStrategy()
            ->failureRateThreshold(50)
            ->intervalToHalfOpen(10)
            ->minimumRequests(10)
            ->timeWindow(30)
            ->adapter($this->adapter)
            ->build();
    }
}
