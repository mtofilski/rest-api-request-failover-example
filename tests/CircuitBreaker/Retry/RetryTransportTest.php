<?php

declare(strict_types=1);

namespace App\Tests\CircuitBreaker\Retry;

use App\CircuitBreaker\Retry\RetryTransport;
use App\CircuitBreaker\Transport\FailedTransport;
use App\CircuitBreaker\Transport\InMemoryFailedTransport;
use App\Tests\Client\Fixtures\ClientFixture;
use App\Tests\ExecutionTrait;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

final class RetryTransportTest extends TestCase
{
    use ExecutionTrait;

    public function testShouldTransportAllMessages(): void
    {
        $storage = new InMemoryFailedTransport();
        $this->executeTimes(
            10,
            function () use ($storage) {
                $storage->store(
                    new Request(
                        'POST',
                        'success'
                    )
                );
            }
        );

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $retryService->retry();

        self::assertEquals(0, $storage->count());
    }

    public function testShouldNotTransportAnyMessageWhenNothingIsInTheStore(): void
    {
        $storage = new InMemoryFailedTransport();

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $retryService->retry();

        self::assertEquals(0, $storage->count());
        self::assertNull($storage->pop());
    }

    public function testShouldTransportAllMessagesAndStoreAgain(): void
    {
        $storage = new InMemoryFailedTransport();
        $this->executeTimes(
            10,
            function () use ($storage) {
                $storage->store(
                    new Request(
                        'POST',
                        'fail'
                    )
                );
            }
        );

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $retryService->retry();

        self::assertEquals(10, $storage->count());
    }

    public function testShouldTransportAllMessagesAndStoreAgainWithRetryHeader(): void
    {
        $storage = new InMemoryFailedTransport();
        $this->executeTimes(
            1,
            function () use ($storage) {
                $storage->store(
                    new Request(
                        'POST',
                        'fail'
                    )
                );
            }
        );

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $retryService->retry();

        $request = $storage->pop();
        self::assertEquals(0, $storage->count());
        self::assertEquals(1, (int)$request->getHeaderLine(FailedTransport::RETRY_HEADER));
    }

    public function testShouldTransportAllMessagesAndStoreAgainWithIncrementedRetryHeader(): void
    {
        $storage = new InMemoryFailedTransport();
        $this->executeTimes(
            1,
            function () use ($storage) {
                $storage->store(
                    new Request(
                        'POST',
                        'fail'
                    )
                );
            }
        );

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $this->executeTimes(
            2,
            function () use ($retryService) {
                $retryService->retry();
            }
        );

        $request = $storage->pop();
        self::assertEquals(0, $storage->count());
        self::assertEquals(2, (int)$request->getHeaderLine(FailedTransport::RETRY_HEADER));
    }

    public function testShouldTryTransportAllMessagesEvenWhenServiceIsStillDown(): void
    {
        $storage = new InMemoryFailedTransport();
        $this->executeTimes(
            10,
            function () use ($storage) {
                $storage->store(
                    new Request(
                        'POST',
                        'fail'
                    )
                );
            }
        );

        $retryService = new RetryTransport(ClientFixture::guzzleClient($storage), $storage);
        $this->executeTimes(
            2,
            function () use ($retryService) {
                $retryService->retry();
            }
        );

        $request = $storage->pop();
        self::assertEquals(9, $storage->count());
        self::assertEquals(2, (int)$request->getHeaderLine(FailedTransport::RETRY_HEADER));
    }
}
