<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\Log\Handler;

use Milceo\Log\Handler\StreamHandler;
use Milceo\Log\LogLevel;
use Milceo\Log\LogRecord;
use PHPUnit\Framework\TestCase;

class StreamHandlerTest extends TestCase
{
    public function testRecordLog()
    {
        $stream = fopen('php://memory', 'a+');

        $handler = new StreamHandler($stream);

        $handler->handle(new LogRecord(LogLevel::INFO, 'Hello World'));

        rewind($stream);

        $content = stream_get_contents($stream);

        self::assertEquals('Hello World' . PHP_EOL, $content);
    }

    public function testRecordException()
    {
        $stream = fopen('php://memory', 'a+');

        $handler = new StreamHandler($stream);

        $handler->handle(new LogRecord(LogLevel::ERROR, 'An error occurred', exception: new \Exception('An error occurred')));

        rewind($stream);

        $content = stream_get_contents($stream);

        self::assertStringContainsString('An error occurred', $content);
        self::assertStringContainsString('Milceo\Tests\Log\Handler\StreamHandlerTest->testRecordException()', $content);
    }
}
