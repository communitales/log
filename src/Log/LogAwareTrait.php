<?php

/**
 * @copyright   Copyright (c) 2019 - 2021 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\Log;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use function array_merge;
use function function_exists;

/**
 * Class LogAwareTrait
 */
trait LogAwareTrait
{

    use LoggerAwareTrait;

    /**
     * Log errors to the logger.
     *
     * @param string               $message
     * @param string               $level
     * @param array<string, mixed> $context
     */
    protected function log(string $message, string $level = LogLevel::ERROR, array $context = []): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Log exception to the logger.
     *
     * @param Throwable            $exception
     * @param string               $level
     * @param array<string, mixed> $context
     */
    protected function logException(Throwable $exception, string $level = LogLevel::ERROR, array $context = []): void
    {
        $context = array_merge($context, ['exception' => $exception, 'trace' => $exception->getTraceAsString()]);

        $this->log($exception->getMessage(), $level, $context);

        // Sentry Integration
        if (function_exists('\Sentry\captureException')) {
            \Sentry\captureException($exception);
        }
    }

}
