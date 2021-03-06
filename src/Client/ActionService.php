<?php

declare(strict_types=1);

namespace App\Client;

use App\Client\Request\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

final class ActionService
{
    private Client $client;

    public function __construct( Request  $request)
    {
        $this->client = $request->getClient();
    }


    public function makeSomeAction(string $actionType): int
    {
        // do stuff
        try {
            $response = $this->client->post($actionType, [
                'body' => '{"test":"OK"}'
            ]);
        } catch (RequestException | ConnectException $e) {
            return $e->getCode();
        }
        return $response->getStatusCode();
    }


}
