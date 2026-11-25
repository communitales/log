<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Communitales GmbH
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\Log;

use Communitales\Component\Log\ExceptionLogger;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;

#[CoversClass(ExceptionLogger::class)]
final class ExceptionLoggerTest extends TestCase
{
    private ExceptionLogger $logger;

    private TestHandler $testHandler;

    protected function setUp(): void
    {
        $this->testHandler = new TestHandler();
        $logger = new Logger('name');
        $logger->pushHandler($this->testHandler);

        $this->logger = new ExceptionLogger($logger);
    }

    public function testDelegatesRegularLogMessages(): void
    {
        $this->logger->log('notice', 'Test message', ['param1' => 'useful debug information']);

        $record = $this->testHandler->getRecords()[0]->toArray();
        unset($record['datetime']);

        $this->assertEquals([
            'level' => Level::Notice->value,
            'message' => 'Test message',
            'context' => ['param1' => 'useful debug information'],
            'level_name' => 'NOTICE',
            'channel' => 'name',
            'extra' => [],
        ], $record);
    }

    public function testLogsExceptionWithDefaultLevel(): void
    {
        $exception = new RuntimeException('Something went wrong');
        $this->logger->logException($exception, context: ['operation' => 'test']);

        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $record = $records[0]->toArray();
        $this->assertSame(Level::Error->value, $record['level']);
        $this->assertSame('Something went wrong', $record['message']);
        $this->assertSame($exception, $record['context']['exception']);
        $this->assertSame('test', $record['context']['operation']);
        $this->assertArrayNotHasKey('trace', $record['context']);
    }

    public function testExceptionCannotBeReplacedThroughContext(): void
    {
        $exception = new RuntimeException('Expected');
        $otherException = new RuntimeException('Unexpected');

        $this->logger->logException($exception, ['exception' => $otherException], LogLevel::WARNING);

        $record = $this->testHandler->getRecords()[0]->toArray();
        $this->assertSame(Level::Warning->value, $record['level']);
        $this->assertSame($exception, $record['context']['exception']);
    }

    public function testLoggerFailurePropagates(): void
    {
        $failure = new RuntimeException('Log failed');
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('log')->willThrowException($failure);

        $this->expectExceptionObject($failure);
        new ExceptionLogger($logger)->logException(new RuntimeException('Original'));
    }
}
