<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log\Handler;

use Milceo\Log\Logger;
use Milceo\Log\LogLevel;
use Milceo\Log\LogRecord;

/**
 * Responsible for handling log records.
 *
 * This class contains a single abstract method {@link AbstractHandler::handle()} that must be implemented by
 * concrete handlers, for example, to write log records to a file (see {@link StreamHandler}),
 * to send them to a remote server, or to save them to a database.
 *
 * To submit the handler to a logger, use the {@link Logger::withHandler()} method.
 *
 * You can set the minimum log level that this handler should handle using the {@link AbstractHandler::withLevel()}
 * method, the format of the log records using {@link AbstractHandler::withFormat()}, and the date format
 * using {@link AbstractHandler::withDateFormat()}.
 *
 * @see StreamHandler
 */
abstract class AbstractHandler
{
    public const LogLevel DEFAULT_LEVEL = LogLevel::INFO;
    public const string DEFAULT_FORMAT = '[{channel}] [{date}] [{class}] - [{level}] {message}';
    public const string DEFAULT_DATE_FORMAT = 'c';

    public LogLevel $level = self::DEFAULT_LEVEL;
    public string $format = self::DEFAULT_FORMAT;
    public string $dateFormat = self::DEFAULT_DATE_FORMAT;

    /**
     * Handles a log record.
     *
     * This method is automatically called by the logger when a log is recorded and
     * the log level is greater than or equal to the handler's level.
     *
     * @param LogRecord $record The log record to handle.
     *
     * @return void
     */
    abstract public function handle(LogRecord $record): void;

    /**
     * Sets the minimum log level that this handler should handle.
     *
     * @param LogLevel $level The minimum log level.
     *
     * @return $this The current instance.
     */
    public function withLevel(LogLevel $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Sets the format of the log records.
     *
     * @param string $format The format of the log records.
     *
     * @return $this The current instance.
     */
    public function withFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Sets the date format of the log records.
     *
     * @param string $dateFormat The date format of the log records.
     *
     * @return $this The current instance.
     */
    public function withDateFormat(string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }
}
