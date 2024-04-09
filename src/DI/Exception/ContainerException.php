<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Exception;

/**
 * Thrown when a key could not be resolved from the container.
 */
class ContainerException extends \RuntimeException
{
    /**
     * ContainerException constructor.
     *
     * @param string          $key       The key that could not be resolved.
     * @param \Throwable|null $exception The exception that caused this exception.
     */
    public function __construct(string $key, \Throwable $exception = null)
    {
        parent::__construct("Could not get '$key' from the container", $exception->getCode(), $exception);
    }
}
