<?php

declare(strict_types=1);

namespace App\Tests\CircuitBreaker\Retry;

use Ackintosh\Ganesha\Exception\RejectedException;
use App\CircuitBreaker\Retry\RetryReport;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RetryReportTest extends TestCase
{
    public function testShouldReturnTotal(): void
    {
        $report = new RetryReport(10);

        self::assertEquals(10, $report->getTotal());
    }

    public function testShouldSaveCode(): void
    {
        $report = new RetryReport(10);

        $report->saveCode(200);

        self::assertEquals(1, $report->succeeded());
        self::assertEquals(0, $report->failed());
        self::assertEquals(9, $report->notExecuted());
    }

    public function testShouldSaveFailedCode(): void
    {
        $report = new RetryReport(5);

        $report->saveCode(200);
        $report->saveCode(404);
        $report->saveCode(500);
        $report->saveCode(401);
        $report->saveCode(504);


        self::assertEquals(1, $report->succeeded());
        self::assertEquals(4, $report->failed());
        self::assertEquals(0, $report->notExecuted());
    }

    public function testShouldSaveFailedReason(): void
    {
        $report =new RetryReport(1);
        $request = $this->createMock(RequestInterface::class);
        $report->saveError($request, new RejectedException('service down'));

        self::assertEquals(1, $report->failed());
        self::assertEquals(0, $report->succeeded());
        self::assertEquals($request, $report->getErrors()->current()->getRequest());
        self::assertEquals('service down', $report->getErrors()->current()->getReason());
    }

    public function testShouldSaveMultipleFailedReason(): void
    {
        $report =new RetryReport(1);
        $request = $this->createMock(RequestInterface::class);
        $anotherRequest = $this->createMock(RequestInterface::class);
        $report->saveError($request, new RejectedException('service down'));
        $report->saveError($anotherRequest, new RejectedException('service still down'));

        $error = $report->getErrors()->current();
        self::assertEquals($request, $error->getRequest());
        self::assertEquals('service down', $error->getReason());
        $report->getErrors()->next();
        $error = $report->getErrors()->current();
        self::assertEquals($anotherRequest, $error->getRequest());
        self::assertEquals('service still down', $error->getReason());
    }
}
