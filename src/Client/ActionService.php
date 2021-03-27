<?php

declare(strict_types=1);

namespace App\Client;

final class ActionService
{

    private ActionRequestInterface $request;

    public function __construct(ActionRequestInterface $request)
    {
        $this->request = $request;
    }


    public function makeSomeAction(string $actionType): int
    {
        // do stuff
        return $this->request->request($actionType);
    }
}
