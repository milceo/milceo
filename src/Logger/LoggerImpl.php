<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Logger;

use function Milceo\Utils\is_enum;

/**
 * Default implementation of the {@link Logger} interface.
 *
 * This class is not meant to be used directly. Use the {@link LoggerFactory} to create a logger.
 */
class LoggerImpl implements Logger
{
    private const string DATE_FORMAT = 'c';

    private string $format;
    private mixed $outputFile;
    private LogLevel $level;

    /**
     * LoggerImpl constructor.
     *
     * @param string   $format     The format of the log messages.
     * @param resource $outputFile The output file where the log messages are written to.
     * @param LogLevel $level      The minimum log level.
     */
    public function __construct(string $format, mixed $outputFile, LogLevel $level)
    {
        $this->format = $format;
        $this->outputFile = $outputFile;
        $this->level = $level;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function log(LogLevel $level, string $message, array $context = []): void
    {
        if ($level->value < $this->level->value) {
            return;
        }

        $line = $this->interpolate($this->format, [
            'datetime' => date(self::DATE_FORMAT),
            'level' => $level,
            'message' => $this->interpolate($message, $context),
        ]);

        fwrite($this->outputFile, $line);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message The message to interpolate.
     * @param array  $context [optional] The context as an associative array where each key is the name of a variable
     *                        and the value is the value of the variable.
     *
     * @return string The interpolated message where the placeholders have been replaced by the values of the variables.
     */
    private function interpolate(string $message, array $context = []): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            if (is_enum($value)) {
                $value = $value->name;
            }

            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = $value;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Returns the format of the log messages.
     *
     * @return string The format of the log messages.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns the output file where the log messages are written to.
     *
     * @return resource The output file where the log messages are written to.
     */
    public function getOutputFile(): mixed
    {
        return $this->outputFile;
    }

    /**
     * Returns the minimum log level.
     *
     * @return LogLevel The minimum log level.
     */
    public function getLevel(): LogLevel
    {
        return $this->level;
    }
}
