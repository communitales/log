<?php

/**
 * @copyright   Copyright (c) 2019 - 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\Log\Test;

use Communitales\Component\Log\LogAwareTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use RuntimeException;

/**
 * Class LogAwareTraitTest
 */
class LogAwareTraitTest extends TestCase
{

    use LogAwareTrait;

    public function testLog(): void
    {
        $this->log('Test message', LogLevel::NOTICE, ['param1' => 'useful debug information']);

        /** @var TestLogger $logger */
        $logger = $this->logger;
        self::assertEquals(
            [
                [
                    'level' => 'notice',
                    'message' => 'Test message',
                    'context' => ['param1' => 'useful debug information'],
                ],
            ],
            $logger->records
        );
    }

    public function testLogException(): void
    {
        $exception = new RuntimeException('Something gone wrong');
        $this->logException($exception);

        /** @var TestLogger $logger */
        $logger = $this->logger;
        self::assertEquals('error', $logger->records[0]['level']);
        self::assertEquals('Something gone wrong', $logger->records[0]['message']);
        self::assertEquals($exception, $logger->records[0]['context']['exception']);
    }

    protected function setUp(): void
    {
        $logger = new TestLogger();
        $this->setLogger($logger);
    }
}
