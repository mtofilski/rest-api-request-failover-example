<?php

declare(strict_types=1);

namespace App\Client;


use App\Client\Request\Repository\FailedRequestRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class ActionService
{
    private Client $client;
    private FailedRequestRepository $repository;

    public function __construct(FailedRequestRepository $repository)
    {
        $this->client = new Client([
            'base_uri' => 'https://localhost:8000',
            'timeout'  => 2.0,
            'verify' => false
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
