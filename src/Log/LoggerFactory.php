<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Responsible for creating {@link Logger} instances.
 *
 * The preferred way to create a logger is by using the {@link LoggerFactory::fromConfiguration()} method. <br>
 * Only use the {@link LoggerFactory::fromProperties()} method if you need to create a logger programmatically.
 */
class LoggerFactory
{
    public const string DEFAULT_FORMAT = '{datetime} [{level}] {message}';
    public const string DEFAULT_FILE = 'php://stdout';
    public const LogLevel DEFAULT_MIN_LOG_LEVEL = LogLevel::INFO;

    /**
     * Creates a logger from the given configuration .ini file.
     *
     * Example of a configuration .ini file:
     * <pre>
     *     format = "{datetime} [{level}] {message}"
     *     output = "php://stdout"
     *     level = "INFO"
     * </pre>
     *
     * The configuration file can also contain a specific section for the logger.
     * In this case, the section name must be <code>logger</code>:
     * <pre>
     *     [logger]
     *     format = "{datetime} [{level}] {message}"
     *     output = "php://stdout"
     *     level = "INFO"
     * </pre>
     *
     * Missing properties will be replaced by their default values.
     *
     * @param string $filename The path to the configuration .ini file.
     *
     * @return Logger The built logger.
     */
    public function fromConfiguration(string $filename): Logger
    {
        if (!is_readable($filename)) {
            throw new \InvalidArgumentException('The configuration file is either not readable or does not exist');
        }

        $properties = @parse_ini_file($filename, true);

        if ($properties === false) {
            throw new \InvalidArgumentException('The configuration file is not a valid .ini file');
        }

        if (isset($properties['logger'])) {
            $properties = $properties['logger'];
        }

        if (isset($properties['level'])) {
            try {
                $properties['level'] = LogLevel::{$properties['level']};
            } catch (\Error) {
                throw new \InvalidArgumentException('The log level is not valid');
            }
        }

        return $this->fromProperties($properties);
    }

    /**
     * Creates a logger from the given properties.
     *
     * @param array $properties [optional] The properties as an associative array, eventually empty.
     *                          Missing properties will be replaced by their default values.
     *
     * @return Logger The built logger.
     */
    public function fromProperties(
        #[ArrayShape(
            [
                'format' => 'string',
                'output' => 'string',
                'level' => LogLevel::class,
            ]
        )] array $properties = [],
    ): Logger
    {
        return new LoggerImpl(
            $properties['format'] ?? self::DEFAULT_FORMAT,
            fopen($properties['output'] ?? self::DEFAULT_FILE, 'a+'),
            $properties['level'] ?? self::DEFAULT_MIN_LOG_LEVEL,
        );
    }
}
