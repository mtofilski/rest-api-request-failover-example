<?php

declare(strict_types=1);

namespace App\Server\Controller;

use App\Server\SuperBusyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SomeResourceController extends AbstractController
{
    public function successEndpoint(SuperBusyService $service): Response
    {
        $service->execute();
        return new Response('OK');
    }
}
