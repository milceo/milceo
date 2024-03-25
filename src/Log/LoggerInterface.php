<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log;

use Milceo\Log\Handler\AbstractHandler;

/**
 * Represents a logger.
 *
 * A logger is responsible for logging messages.
 *
 * The logger can have multiple {@link AbstractHandler}s that will be called in the order they were added. <br>
 * Handlers are submitted to the logger using the {@link Logger::withHandler()} method.
 *
 * The logger can also be identified by his name that may optionally be included in the log records.
 *
 * To log a message, use the {@link LoggerInterface::log()} method.
 */
interface LoggerInterface
{
    /**
     * Logs a message with the given {@link LogLevel}.
     *
     * Messages are logged to all submitted handlers that have a log level greater than or equal to the log level of
     * the message.
     *
     * Messages are formatted using a format string (customizable for each {@link AbstractHandler}) that can contain the
     * following placeholders:
     * <ul>
     *     <li>{channel}: The channel to which the log record was submitted (i.e. the logger name).</li>
     *     <li>{date}: The date when the log record was created.</li>
     *     <li>
     *         {class}: The stack trace of the log record (e.g. the class and line where the log record was created).
     *     </li>
     *     <li>{level}: The level of the log record.</li>
     *     <li>{message}: The message of the log record (after placeholders have been replaced by their values).</li>
     * </ul>
     *
     * The default format is {@link AbstractHandler::DEFAULT_FORMAT}.
     *
     * The date format can also be customized (default to {@link AbstractHandler::DEFAULT_DATE_FORMAT}).
     * It applies to the {date} placeholder, as well as every other {@link \DateTime} objects given in the context
     * array.
     *
     * Example:
     * <pre>
     *     $stream = fopen('php://stdout', 'w');
     *     $handler = (new StreamHandler($stream))
     *         ->withFormat('[{date}] - [{level}] {message}')
     *         ->withDateFormat('Y-m-d H:i:s');
     *
     *     $logger = (new Logger())
     *         ->withHandler($handler);
     * </pre>
     *
     * The message "User John Doe has logged in" with a log level of INFO will append the following line to the
     * console:
     * <code>[2024-01-01 12:00:00] - [INFO] User John Doe has logged in</code>
     *
     * @param LogLevel  $level   The log level of the message. Determines the severity of the log message.
     *                           Messages with a lower log level than the minimum log level will be ignored.
     * @param string    $message The message to log. The message may contain placeholders that will be replaced
     *                           by the values of the variables in the context array. Placeholders are specified as
     *                           <code>{placeholder}</code> where <code>placeholder</code> is the name of the variable
     *                           to be replaced by its value from the context array.
     * @param array     $context [optional] The message context as an associative array where each key
     *                           is the name of a variable and the value is the value of the variable.
     *                           Variables that cannot be cast to string will be ignored.
     *
     * @param \DateTime $date    [optional] The date at which the message is logged.
     *
     * @return void
     */
    public function log(LogLevel $level, string $message, array $context = [], \DateTime $date = new \DateTime()): void;
}
