<?php

declare(strict_types=1);

namespace App\Client;

use Exception;
use GuzzleHttp\ClientInterface;

final class ActionService
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }


    public function makeSomeAction(string $actionType): int
    {
        // do stuff
        try {
            $response = $this->client->request('POST', $actionType, [
                'body' => '{"test":"OK"}'
            ]);
        } catch (Exception $e) {
            return $e->getCode();
        }
        return $response->getStatusCode();
    }


}
