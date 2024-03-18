<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\Logger;

use Milceo\Logger\LoggerFactory;
use Milceo\Logger\LoggerImpl;
use Milceo\Logger\LogLevel;
use Milceo\Tests\Logger\Enums\Gender;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    private const string LOG_FILE = '/tmp/log.txt';

    private const string CONFIGURATION_FILE = __DIR__ . '/Configuration/logger.ini';
    private const string EMPTY_CONFIGURATION_FILE = __DIR__ . '/Configuration/empty_logger.ini';
    private const string YAML_CONFIGURATION_FILE = __DIR__ . '/Configuration/logger.yaml';
    private const string INVALID_CONFIGURATION_FILE = __DIR__ . '/Configuration/invalid_logger.ini';

    public static function provideLogLevels(): array
    {
        return array_map(
            fn(LogLevel $level): array => [$level],
            LogLevel::cases(),
        );
    }

    #[DataProvider('provideLogLevels')]
    public function testLog(LogLevel $level): void
    {
        $logger = (new LoggerFactory())->fromProperties([
            'format' => '[{level}] {message}',
            'output' => self::LOG_FILE,
            'level' => LogLevel::DEBUG,
        ]);

        $logger->log(
            $level,
            '{username} with gender {gender} has logged in',
            ['gender' => Gender::MALE, 'username' => 'John Doe'],
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile);

        self::assertEquals("[$level->name] John Doe with gender MALE has logged in", $content);
    }

    public function testLogWithLevelBelowMinimum(): void
    {
        $logger = (new LoggerFactory())->fromProperties(
            [
                'format' => '[{level}] {message}',
                'output' => self::LOG_FILE,
                'level' => LogLevel::WARNING,
            ],
        );

        $logger->log(
            LogLevel::INFO,
            'This message should not be logged',
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile);

        self::assertEquals('', $content);
    }

    public function testLogWithProperties(): void
    {
        $logger = (new LoggerFactory())->fromProperties(
            [
                'format' => '[{level}] {message}',
                'output' => self::LOG_FILE,
                'level' => LogLevel::INFO,
            ],
        );

        $logger->log(
            LogLevel::INFO,
            'John Doe has logged in',
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile, offset: 0);

        self::assertEquals('[INFO] John Doe has logged in', $content);
    }

    public function testLogWithEmptyProperties()
    {
        /**
         * @var LoggerImpl $logger
         */
        $logger = (new LoggerFactory())->fromProperties();

        self::assertEquals(LoggerFactory::DEFAULT_FORMAT, $logger->getFormat());
        self::assertEquals(LoggerFactory::DEFAULT_FILE, stream_get_meta_data($logger->getOutputFile())['uri']);
        self::assertEquals(LogLevel::INFO, $logger->getLevel());
    }

    public function testLogWithConfigurationFile(): void
    {
        $logger = (new LoggerFactory())->fromConfiguration(self::CONFIGURATION_FILE);

        $logger->log(
            LogLevel::INFO,
            'John Doe has logged in',
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile);

        self::assertEquals('[INFO] John Doe has logged in', $content);
    }

    public function testLogWithEmptyConfigurationFile()
    {
        $loggerFactoryMock = $this->getMockBuilder(LoggerFactory::class)
            ->onlyMethods(['fromProperties'])
            ->getMock();

        $loggerFactoryMock->expects($this->once())
            ->method('fromProperties')
            ->with([]);

        $loggerFactoryMock->fromConfiguration(self::EMPTY_CONFIGURATION_FILE);
    }

    public function testLogWithYamlConfigurationFile()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new LoggerFactory())->fromConfiguration(self::YAML_CONFIGURATION_FILE);
    }

    public function testLogWithInvalidConfigurationFile()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new LoggerFactory())->fromConfiguration(self::INVALID_CONFIGURATION_FILE);
    }

    public function testLogWithUnknownConfigurationFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new LoggerFactory())->fromConfiguration('/path/to/invalid/file');
    }

    public function testLogWithEmptyContext(): void
    {
        $logger = (new LoggerFactory())->fromProperties([
            'format' => '[{level}] {message}',
            'output' => self::LOG_FILE,
        ]);

        $logger->log(
            LogLevel::INFO,
            'John Doe has logged in',
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile, offset: 0);

        self::assertEquals('[INFO] John Doe has logged in', $content);
    }

    public function testLogWithInvalidContext(): void
    {
        $logger = (new LoggerFactory())->fromProperties([
            'format' => '[{level}] {message}',
            'output' => self::LOG_FILE,
        ]);

        $logger->log(
            LogLevel::INFO,
            '{username} has logged in',
            ['username' => []],
        );

        $logFile = fopen(self::LOG_FILE, 'r');
        $content = stream_get_contents(stream: $logFile, offset: 0);

        self::assertEquals('[INFO] {username} has logged in', $content);
    }

    protected function tearDown(): void
    {
        file_put_contents(self::LOG_FILE, '');
    }
}
