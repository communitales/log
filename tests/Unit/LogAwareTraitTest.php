<?php

/**
 * @copyright   Copyright (c) 2019 - 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\Log;

use Communitales\Component\Log\LogAwareTrait;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use RuntimeException;

/**
 * Class LogAwareTraitTest
 */
class LogAwareTraitTest extends TestCase
{
    use LogAwareTrait;

    private TestHandler $testHandler;

    #[Override]
    protected function setUp(): void
    {
        $this->testHandler = new TestHandler();

        $logger = new Logger('name');
        $logger->pushHandler($this->testHandler);

        $this->setLogger($logger);
    }

    public function testLog(): void
    {
        $this->log('Test message', LogLevel::NOTICE, ['param1' => 'useful debug information']);

        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);

        $record = $records[0]->toArray();
        unset($record['datetime']);

        self::assertEquals(
            [
                'level' => Level::Notice->value,
                'message' => 'Test message',
                'context' => ['param1' => 'useful debug information'],
                'level_name' => 'NOTICE',
                'channel' => 'name',
                'extra' => [],
            ],
            $record
        );
    }

    public function testLogException(): void
    {
        $exception = new RuntimeException('Something gone wrong');
        $this->logException($exception);

        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);

        $record = $records[0]->toArray();

        self::assertEquals(Level::Error->value, $record['level']);
        self::assertEquals('Something gone wrong', $record['message']);
        self::assertEquals($exception, $record['context']['exception']);
    }
}
