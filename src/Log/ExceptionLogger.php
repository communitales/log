<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Communitales GmbH
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

final class ExceptionLogger extends AbstractLogger implements ExceptionLoggerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logException(
        Throwable $exception,
        array $context = [],
        string $level = LogLevel::ERROR,
    ): void {
        $this->logger->log(
            $level,
            $exception->getMessage(),
            ['exception' => $exception] + $context,
        );

    }
}
