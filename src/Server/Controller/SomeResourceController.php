<?php

declare(strict_types=1);

namespace App\Server\Controller;

use App\Server\SuperBusyService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SomeResourceController extends AbstractController
{
    public function successEndpoint(SuperBusyService $service): Response
    {
        $service->execute();
        return new Response('OK');
    }

    public function failEndpoint(SuperBusyService $service): Response
    {
        $service->execute();
        return new Response('FAIL', 500);
    }

    public function unstableEndpoint(SuperBusyService $service, Request $request, LoggerInterface $logger): Response
    {
        $logger->info('saving post', [(string)$request->getContent()]);
        $logger->info('saving headers', $request->headers->all());
        if(random_int(0,10) === 5) {
            return $this->failEndpoint($service);
        }
        return $this->successEndpoint($service);
    }
}
