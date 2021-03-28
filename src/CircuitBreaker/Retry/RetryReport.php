<?php

declare(strict_types=1);

namespace App\CircuitBreaker\Retry;

use Psr\Http\Message\RequestInterface;
use Throwable;

final class RetryReport
{
    private int $total;
    private array $responseCode = [];
    private RetryErrors $errors;

    public function __construct(int $total)
    {
        $this->total = $total;
        $this->errors = new RetryErrors();
    }

    public function saveCode(int $responseCode): void
    {
        if (!isset($this->responseCode[$responseCode])) {
            $this->responseCode[$responseCode] = 0;
        }
        $this->responseCode[$responseCode]++;
    }

    public function saveError(RequestInterface $request, Throwable $exception): void
    {
        $this->saveCode($exception->getCode());
        $this->errors->add(
            new RetryError($request, $exception->getMessage())
        );
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function succeeded(): int
    {
        return $this->responseCode[200] ?? 0;
    }

    public function failed(): int
    {
        $failedCounter = 0;
        foreach ($this->responseCode as $code => $count) {
            if ($code !== 200) {
                $failedCounter += $count;
            }
        }
        return $failedCounter;
    }

    public function notExecuted(): int
    {
        return $this->total - $this->succeeded() - $this->failed();
    }

    public function getErrors(): RetryErrors
    {
        return $this->errors;
    }
}
