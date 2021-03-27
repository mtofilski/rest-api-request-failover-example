<?php

declare(strict_types=1);

namespace App\Client\Infrastructure;

use Ackintosh\Ganesha\Exception\RejectedException;
use App\Client\ActionRequestInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

final class HttpActionRequest implements ActionRequestInterface
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }


    public function request(string $actionType): int
    {
        try {
            $response = $this->client->request(
                'POST',
                $actionType,
                [
                    'body' => '{"test":"OK"}'
                ]
            );
        } catch (GuzzleException | RejectedException $e) {
            return $e->getCode();
        }
        return $response->getStatusCode();
    }
}
