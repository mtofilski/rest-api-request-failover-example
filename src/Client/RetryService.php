<?php

declare(strict_types=1);

namespace App\Client;

use App\Client\Request\Repository\FailedRequestRepository;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;

final class RetryService
{
    private FailedRequestRepository $repository;
    private Client $client;

    public function __construct(FailedRequestRepository $repository)
    {
        $this->repository = $repository;
        $this->client = new Client([
            'base_uri' => 'https://localhost:8000',
            'timeout'  => 2.0,
            'verify' => false
        ]);
    }


    /** @return RequestInterface[] */
    public function toRetry(): array
    {
        $requestList = [];
        while (($request = $this->repository->pop()) !== null) {
            $requestList[] = $request;
        }
        return $requestList;
    }

    public function retry(RequestInterface $request): int
    {

        $response = $this->client->sendRequest(
            $request->withHeader('X-RETRY-ACTION', 1)
        );
        return $response->getStatusCode();
    }
}
