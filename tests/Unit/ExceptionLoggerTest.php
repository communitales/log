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
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use RuntimeException;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;

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

        $this->logger->logException($exception, LogLevel::WARNING, ['exception' => $otherException]);

        $record = $this->testHandler->getRecords()[0]->toArray();
        $this->assertSame(Level::Warning->value, $record['level']);
        $this->assertSame($exception, $record['context']['exception']);
    }

    public function testReportsExceptionToSentry(): void
    {
        $exception = new RuntimeException('Report to Sentry');
        $previousHub = SentrySdk::getCurrentHub();
        $hub = $this->createMock(HubInterface::class);
        $hub
            ->expects($this->once())
            ->method('captureException')
            ->with($this->identicalTo($exception), null)
        ;

        SentrySdk::setCurrentHub($hub);

        try {
            $this->logger->logException($exception);
        } finally {
            SentrySdk::setCurrentHub($previousHub);
        }
    }
}
