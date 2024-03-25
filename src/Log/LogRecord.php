<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log;

/**
 * Represents a log record.
 */
readonly class LogRecord
{
    /**
     * LogRecord constructor.
     *
     * @param LogLevel        $level     The log level.
     * @param string          $message   The log message (formatted).
     * @param string          $channel   The log channel, defaults to 'APP'.
     * @param \DateTime       $date      The date and time of the log record.
     * @param array           $backtrace The log backtrace, if any.
     * @param \Exception|null $exception The exception associated with the log record, if any.
     */
    public function __construct(
        public LogLevel $level,
        public string $message,
        public string $channel = 'APP',
        public \DateTime $date = new \DateTime(),
        public array $backtrace = [],
        public ?\Exception $exception = null,
    ) {

    }
}
