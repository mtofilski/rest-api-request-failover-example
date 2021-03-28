<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Retry;

use Ackintosh\Ganesha\Exception\RejectedException;
use App\CircuitBreaker\Transport\FailedTransport;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

final class RetryTransport
{

    private FailedTransport $failedTransport;
    private ClientInterface $client;

    public function __construct(ClientInterface $client, FailedTransport $failedTransport)
    {
        $this->failedTransport = $failedTransport;
        $this->client = $client;
    }

    public function retry(): bool
    {
        $requestsToTransport = $this->failedTransport->count();
        try {
            for ($i = 0; $i < $requestsToTransport; $i++) {
                $request = $this->failedTransport->pop();
                if ($request) {
                    $currentRetryCounter = 1 + (int)$request->getHeaderLine(FailedTransport::RETRY_HEADER);
                    $this->client->send($request->withHeader(FailedTransport::RETRY_HEADER, $currentRetryCounter));
                }
            }
        } catch (GuzzleException | RejectedException $e) {
            return false;
        }
        return true;
    }

    public function retryForce(): bool
    {
        $requestsToTransport = $this->failedTransport->count();

            for ($i = 0; $i < $requestsToTransport; $i++) {
                $request = $this->failedTransport->pop();
                if ($request) {
                    $currentRetryCounter = 1 + (int)$request->getHeaderLine(FailedTransport::RETRY_HEADER);
                    try {
                        $this->client->send($request->withHeader(FailedTransport::RETRY_HEADER, $currentRetryCounter));
                    } catch (GuzzleException | RejectedException $e) {
                        continue;
                    }
                }
            }

        return true;
    }
}
