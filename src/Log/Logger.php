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
 * Represents a logger.
 *
 * A logger is responsible for logging messages with a given {@link LogLevel}.
 *
 * A logger instance can be obtained from a {@link LoggerFactory}.
 *
 * A logger can be configured with :
 * <ul>
 *     <li>A log format: A string that specifies how the log messages will be formatted.</li>
 *     <li>An output file: The file where the log messages will be written to.</li>
 *     <li>A log level: The minimum log level required for a message to be logged.</li>
 * </ul>
 *
 * The log format may contain different placeholders:
 * <ul>
 *     <li><code>{datetime}</code>: The date and time when the message was logged (in ISO 8601 format).</li>
 *     <li><code>{level}</code>: The log level of the message.</li>
 *     <li><code>{message}</code>: The logged message.</li>
 * </ul>
 *
 * The log format: <code>"{datetime} [{level}] {message}"</code> could produce the following log:
 * <code>2024-01-01T12:00:00+01:00 [INFO] User John Doe has logged in</code>
 */
interface Logger
{
    /**
     * Logs a message with the given {@link LogLevel}.
     *
     * The message may contain placeholders that will be replaced by the values of the variables in the context array.
     *
     * The following code snippet shows how to log a message with placeholders:
     * <pre>
     *     $username = 'John Doe';
     *     $logger->log(
     *         LogLevel::INFO,
     *         'User {username} has logged in',
     *         ['username' => $username]
     *      );
     * </pre>
     *
     * Using the default log format, the log message could be:
     * <code>2024-01-01T12:00:00+01:00 [INFO] User John Doe has logged in</code>
     *
     * @param LogLevel $level   The log level of the message. Determines the severity of the log message.
     *                          Messages with a lower log level than the minimum log level will be ignored.
     * @param string   $message The message to log. Placeholders are specified as <code>{placeholder}</code>
     *                          where <code>placeholder</code> is the name of the variable to be replaced by its value
     *                          from the context array. Variables that cannot be cast to string will be ignored.
     * @param array    $context [optional] The message context as an associative array where each key
     *                          is the name of a variable and the value is the value of the variable.
     *                          Variables that cannot be cast to string will be ignored.
     *
     * @return void
     */
    public function log(LogLevel $level, string $message, array $context = []): void;
}
