<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Communitales GmbH
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\Log;

use Communitales\Component\Log\ExceptionLogger;
use Communitales\Component\Log\ExceptionLoggerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
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

}
