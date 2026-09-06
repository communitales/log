<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2019 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        string $level = LogLevel::ERROR,
        array $context = [],
    ): void;
}
