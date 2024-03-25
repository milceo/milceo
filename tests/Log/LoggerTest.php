<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log {
    function debug_backtrace(): array
    {
        return [
            [
                'class' => 'Milceo\Tests\Log\LoggerTest',
                'line' => 42,
            ],
        ];
    }
}

namespace Milceo\Tests\Log {

    use Milceo\Log\Handler\StreamHandler;
    use Milceo\Log\Logger;
    use Milceo\Log\LogLevel;
    use Milceo\Tests\Log\Assets\Gender;
    use Milceo\Tests\Log\Assets\User;
    use Milceo\Tests\Log\Assets\UserWithToStringMethod;
    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    class LoggerTest extends TestCase
    {
        public static function provideLogLevels(): array
        {
            return array_map(
                fn(LogLevel $level): array => [$level],
                LogLevel::cases(),
            );
        }

        #[DataProvider('provideLogLevels')]
        public function testRecordLog(LogLevel $level)
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withName('Milceo')
                ->withHandler((new StreamHandler($stream))->withLevel(LogLevel::DEBUG));

            self::assertEquals('Milceo', $logger->getName());

            $logger->log($level, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals("[Milceo] [2024-01-01T00:00:00+00:00] [Milceo\Tests\Log\LoggerTest:42] - [$level->name] Hello World" . PHP_EOL, $content);
        }

        public function testLogIsNotRecordedIfLevelIsBelowMinimum()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler(
                    (new StreamHandler($stream))
                        ->withLevel(LogLevel::WARNING)
                        ->withFormat('[{level}] {message}'),
                );

            $logger->log(LogLevel::DEBUG, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));
            $logger->log(LogLevel::INFO, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));
            $logger->log(LogLevel::WARNING, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[WARNING] Hello World' . PHP_EOL, $content);
        }

        public function testRecordLogWithCustomFormat()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'))
                ->withHandler((new StreamHandler($stream))->withFormat('{message}'));

            $logger->log(LogLevel::INFO, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] Hello World' . PHP_EOL . 'Hello World' . PHP_EOL, $content);
        }

        public function testRecordLogWithCustomDateFormat()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{date}] [{level}] {message}')->withDateFormat('Y-m-d'))
                ->withHandler((new StreamHandler($stream))->withFormat('[{date}] [{level}] {message}')->withDateFormat('Y-m-d H:i:s'));

            $logger->log(LogLevel::INFO, 'Hello World', date: new \DateTime('2024-01-01 00:00:00'));

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[2024-01-01] [INFO] Hello World' . PHP_EOL . '[2024-01-01 00:00:00] [INFO] Hello World' . PHP_EOL, $content);
        }

        public function testRecordLogWithContext()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, '{name} ({gender}) is {age} years old', [
                'name' => 'John Doe',
                'gender' => Gender::MALE,
                'age' => 42,
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] John Doe (MALE) is 42 years old' . PHP_EOL, $content);
        }

        public function testRecordLogWithDateContextValue()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}')->withDateFormat('Y-m-d'));

            $logger->log(LogLevel::INFO, 'Today is {date}', [
                'date' => new \DateTime('2024-01-01 00:00:00'),
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] Today is 2024-01-01' . PHP_EOL, $content);
        }

        public function testRecordLogWithArrayContextValue()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, 'Languages: {languages}', [
                'languages' => ['PHP', 'Java', 'Python'],
            ]);

            $logger->log(LogLevel::INFO, 'Languages: {languages}', [
                'languages' => [
                    'PHP' => 'Hypertext Preprocessor',
                    'Java' => 'Just Another Vague Acronym',
                    'Python' => 'Monty Python',
                ],
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] Languages: ["PHP","Java","Python"]' . PHP_EOL . '[INFO] Languages: {"PHP":"Hypertext Preprocessor","Java":"Just Another Vague Acronym","Python":"Monty Python"}' . PHP_EOL, $content);
        }

        public function testRecordLogWithObjectContextValue()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, 'User : {user}', [
                'user' => new User('John Doe'),
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] User : [object Milceo\Tests\Log\Assets\User]' . PHP_EOL, $content);
        }

        public function testRecordLogWithObjectWithToStringMethodContextValue()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, 'User : {user}', [
                'user' => new UserWithToStringMethod('John Doe'),
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] User : John Doe' . PHP_EOL, $content);
        }

        public function testRecordLogWithClosureContextValue()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, 'User : {user}', [
                'user' => function () {

                },
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] User : [object Closure]' . PHP_EOL, $content);
        }

        public function testRecordLogWithUnknownContextKey()
        {
            $stream = fopen('php://memory', 'a+');

            $logger = (new Logger())
                ->withHandler((new StreamHandler($stream))->withFormat('[{level}] {message}'));

            $logger->log(LogLevel::INFO, '{name} logged in', [
                'username' => 'John Doe',
            ]);

            rewind($stream);

            $content = stream_get_contents($stream);

            self::assertEquals('[INFO] {name} logged in' . PHP_EOL, $content);
        }
    }
}
