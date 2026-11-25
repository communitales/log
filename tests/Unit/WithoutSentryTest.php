<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2019 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\Log;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

#[CoversNothing]
final class WithoutSentryTest extends TestCase
{
    public function testLoggingWithoutSentryInstalled(): void
    {
        $script = <<<'PHP'
            $root = $argv[1];
            require $root . '/vendor/psr/log/src/LoggerInterface.php';
            require $root . '/vendor/psr/log/src/LoggerTrait.php';
            require $root . '/vendor/psr/log/src/AbstractLogger.php';
            require $root . '/vendor/psr/log/src/NullLogger.php';
            require $root . '/vendor/psr/log/src/LogLevel.php';
            require $root . '/src/Log/ExceptionLoggerInterface.php';
            require $root . '/src/Log/ExceptionLogger.php';
            if (function_exists('Sentry\\captureException')) {
                exit(1);
            }
            $logger = new Communitales\Component\Log\ExceptionLogger(new Psr\Log\NullLogger());
            $logger->logException(new RuntimeException('First'));
            $logger->logException(new RuntimeException('Second'));
            PHP;
        $process = new Process([PHP_BINARY, '-n', '-r', $script, dirname(__DIR__, 2)]);
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
    }
}
