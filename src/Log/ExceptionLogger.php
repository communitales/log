<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2019 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

use function function_exists;
use function Sentry\captureException;

final class ExceptionLogger extends AbstractLogger implements ExceptionLoggerInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /** @param array<string, mixed> $context */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    /** @param array<string, mixed> $context */
    public function logException(
        Throwable $exception,
        string $level = LogLevel::ERROR,
        array $context = [],
    ): void {
        $this->logger->log(
            $level,
            $exception->getMessage(),
            ['exception' => $exception] + $context,
        );

        if (function_exists('Sentry\captureException')) {
            captureException($exception);
        }
    }
}
