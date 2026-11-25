<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2019 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\Log;

use Communitales\Component\Log\ExceptionLogger;
use Communitales\Component\Log\ExceptionLoggerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Sentry\Monolog\ExceptionToSentryIssueHandler;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

#[CoversClass(ExceptionLogger::class)]
final class SymfonyIntegrationTest extends TestCase
{
    public function testReadmeConfigurationWiresLogger(): void
    {
        $readme = file_get_contents(__DIR__ . '/../../README.md');
        $this->assertIsString($readme);
        $this->assertSame(1, preg_match('/## Setup.*?```yaml\s*\n(.*?)```/s', $readme, $matches));
        $file = tempnam(sys_get_temp_dir(), 'log-symfony-');
        $this->assertIsString($file);
        file_put_contents($file, $matches[1]);

        try {
            $container = new ContainerBuilder();
            new YamlFileLoader($container, new FileLocator())->load($file);
            $container->register('logger', Logger::class)->setArguments(['test'])->setPublic(true);
            $container->getAlias(ExceptionLoggerInterface::class)->setPublic(true);
            $container->compile(true);

            $logger = $container->get('logger');
            $this->assertInstanceOf(Logger::class, $logger);
            $handler = new TestHandler();
            $logger->pushHandler($handler);
            $exceptionLogger = $container->get(ExceptionLoggerInterface::class);
            $this->assertInstanceOf(ExceptionLoggerInterface::class, $exceptionLogger);

            $exceptionLogger->logException(new RuntimeException('Symfony integration'));
            $this->assertCount(1, $handler->getRecords());
        } finally {
            unlink($file);
        }
    }

    public function testOfficialSentryHandlerReceivesExceptionFromLogger(): void
    {
        $exception = new RuntimeException('Report to Sentry');
        $hub = $this->createMock(HubInterface::class);
        $hub->expects($this->once())->method('withScope')->willReturnCallback(
            static function (callable $callback): void {
                $callback(new Scope());
            },
        );
        $hub->expects($this->once())->method('captureException')->with($this->identicalTo($exception));
        $logger = new Logger('test', [new ExceptionToSentryIssueHandler($hub, Level::Error)]);

        new ExceptionLogger($logger)->logException($exception);
    }

}
