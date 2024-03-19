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
 * Describes the severity of a log message according to {@link http://tools.ietf.org/html/rfc5424 RFC 5424}.
 *
 * Log levels are ordered by priority. The most critical log level is <code>EMERGENCY</code>,
 * the least critical log level is <code>DEBUG</code>.
 *
 * The default minimum log level is <code>INFO</code>.
 */
enum LogLevel: int
{
    case EMERGENCY = 7;
    case ALERT = 6;
    case CRITICAL = 5;
    case ERROR = 4;
    case WARNING = 3;
    case NOTICE = 2;
    case INFO = 1;
    case DEBUG = 0;
}
