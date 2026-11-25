<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Communitales GmbH
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

interface ExceptionLoggerInterface extends LoggerInterface
{
    /** @param array<string, mixed> $context */
    public function logException(
        Throwable $exception,
        array $context = [],
        string $level = LogLevel::ERROR,
    ): void;
}
