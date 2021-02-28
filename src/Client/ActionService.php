<?php

declare(strict_types=1);

namespace App\Client;


use GuzzleHttp\Client;

final class ActionService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://localhost:8000',
            'timeout'  => 2.0,
            'verify' => false
        ]);
    }


    public function makeSomeAction(string $actionType): int
    {
        // do stuff

        $response = $this->client->post($actionType);
        return $response->getStatusCode();
    }

}
